<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractNativeFixture;
use SS6\ShopBundle\Component\System\System;

class DbCollationsDataFixture extends AbstractNativeFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$system = $this->get(System::class);
		/* @var $system \SS6\ShopBundle\Component\System\System */

		if ($system->isWindows()) {
			$this->executeNativeQuery('CREATE COLLATION "cs_CZ" (LOCALE="Czech")');
			$this->executeNativeQuery('CREATE COLLATION "de_DE" (LOCALE="German")');
			$this->executeNativeQuery('CREATE COLLATION "en_US" (LOCALE="English_US")');
			$this->executeNativeQuery('CREATE COLLATION "hu_HU" (LOCALE="Hungarian")');
			$this->executeNativeQuery('CREATE COLLATION "pl_PL" (LOCALE="Polish")');
			$this->executeNativeQuery('CREATE COLLATION "sk_SK" (LOCALE="Slovak")');
		} else {
			$this->executeNativeQuery('CREATE COLLATION "cs_CZ" (LOCALE="cs_CZ.utf8")');
			$this->executeNativeQuery('CREATE COLLATION "de_DE" (LOCALE="de_DE.utf8")');
			$this->executeNativeQuery('CREATE COLLATION "en_US" (LOCALE="en_US.utf8")');
			$this->executeNativeQuery('CREATE COLLATION "hu_HU" (LOCALE="hu_HU.utf8")');
			$this->executeNativeQuery('CREATE COLLATION "pl_PL" (LOCALE="pl_PL.utf8")');
			$this->executeNativeQuery('CREATE COLLATION "sk_SK" (LOCALE="sk_SK.utf8")');
		}
	}

}
