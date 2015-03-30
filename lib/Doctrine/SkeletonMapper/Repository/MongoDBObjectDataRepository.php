<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\SkeletonMapper\Repository;

use Doctrine\SkeletonMapper\ObjectManagerInterface;
use MongoCollection;

abstract class MongoDBObjectDataRepository extends ObjectDataRepository
{
    /**
     * @var \Doctrine\SkeletonMapper\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \MongoCollection
     */
    private $mongoCollection;

    /**
     * @param \Doctrine\SkeletonMapper\ObjectManagerInterface $objectManager
     * @param \MongoCollection $mongoCollection
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        MongoCollection $mongoCollection)
    {
        $this->objectManager = $objectManager;
        $this->mongoCollection = $mongoCollection;
    }

    /**
     * @return string
     */
    abstract public function getClassName();

    public function find($id)
    {
        if (!is_array($id)) {
            $id = array('_id' => $id);
        }

        return $this->mongoCollection->findOne($id);
    }

    public function findByObject($object)
    {
        return $this->find($this->getObjectIdentifier($object));
    }

    public function findAll()
    {
        return $this->mongoCollection->find(array());
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $cursor = $this->mongoCollection->find($criteria);

        if ($orderBy !== null) {
            $cursor->sort($orderBy);
        }

        if ($limit !== null) {
            $cursor->limit($limit);
        }

        if ($offset !== null) {
            $cursor->skip($offset);
        }

        return iterator_to_array($cursor);
    }

    public function findOneBy(array $criteria)
    {
        return $this->mongoCollection->findOne($criteria);
    }

    /**
     * @param object $object
     *
     * @return array
     */
    private function getObjectIdentifier($object)
    {
        return $this->objectManager
            ->getRepository($this->getClassName())
            ->getObjectIdentifier($object);
    }
}
