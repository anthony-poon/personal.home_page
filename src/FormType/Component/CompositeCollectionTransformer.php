<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 3/9/2018
 * Time: 4:11 PM
 */

namespace App\FormType\Component;

use Symfony\Component\Form\DataTransformerInterface;

class CompositeCollectionTransformer implements DataTransformerInterface {
    private $allowDuplicate = false;

    /**
     * @return bool
     */
    public function isAllowDuplicate(): bool {
        return $this->allowDuplicate;
    }

    /**
     * @param bool $allowDuplicate
     * @return CompositeCollectionTransformer
     */
    public function setAllowDuplicate(bool $allowDuplicate): CompositeCollectionTransformer {
        $this->allowDuplicate = $allowDuplicate;
        return $this;
    }


	public function transform($arr) {
		return $arr;
	}

	public function reverseTransform($arr) {
		$rtn = [];
		foreach ($arr as $item) {
		    if ($this->allowDuplicate || (!$this->allowDuplicate && !in_array($item, $rtn))) {
                $rtn[] = $item;
            }
		}
		return $rtn;
	}


}