<?php

namespace SS6\ShopBundle\Model\Category;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use SS6\ShopBundle\Model\Category\Category;

class CategoryRepository extends NestedTreeRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
		$classMetadata = $this->em->getClassMetadata(Category::class);
		parent::__construct($this->em, $classMetadata);
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getCategoryRepository() {
		return $this->em->getRepository(Category::class);
	}

	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getAllQueryBuilder() {
		return $this->getCategoryRepository()
			->createQueryBuilder('c')
			->where('c.parent IS NOT NULL')
			->orderBy('c.lft');
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAll() {
		return $this->getAllQueryBuilder()
			->getQuery()
			->getResult();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getRootCategory() {
		return $this->getCategoryRepository()->findOneBy(['parent' => null]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $categoryBranch
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllWithoutBranch(Category $categoryBranch) {
		return $this->getAllQueryBuilder()
			->andWhere('c.lft < :branchLft OR c.rgt > :branchRgt')
			->setParameter('branchLft', $categoryBranch->getLft())
			->setParameter('branchRgt', $categoryBranch->getRgt())
			->getQuery()
			->execute();
	}

	/**
	 * @param int $categoryId
	 * @return \SS6\ShopBundle\Model\Category\Category|null
	 */
	public function findById($categoryId) {
		return $this->getCategoryRepository()->find($categoryId);
	}

	/**
	 * @param int $categoryId
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getById($categoryId) {
		$category = $this->findById($categoryId);

		if ($category === null) {
			throw new \SS6\ShopBundle\Model\Category\Exception\CategoryNotFoundException($categoryId);
		}

		return $category;
	}

	/**
	 * @param string $locale
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllInRootWithTranslation($locale) {
		return $this->getAllWithTranslationQueryBuilder($locale)
			->andWhere('c.level = 1')
			->getQuery()
			->execute();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllInRootEagerLoaded() {
		$allCategories = $this->getAllQueryBuilder()
			->join('c.translations', 'ct')
			->getQuery()
			->execute();

		$rootCategories = [];
		foreach ($allCategories as $cateogry) {
			if ($cateogry->getLevel() === 1) {
				$rootCategories[] = $cateogry;
			}
		}

		return $rootCategories;
	}

	/**
	 * @param string $locale
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getAllWithTranslationQueryBuilder($locale) {
		$qb = $this->getAllQueryBuilder()
			->join('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
			->andWhere('ct.name IS NOT NULL');
		$qb->setParameter('locale', $locale);

		return $qb;
	}

}
