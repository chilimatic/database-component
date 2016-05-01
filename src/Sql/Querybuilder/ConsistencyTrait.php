<?php
namespace chilimatic\lib\Database\Sql\Querybuilder;

use chilimatic\lib\Database\Exception\QueryBuilderException;

/**
 *
 * @author j
 * Date: 4/21/15
 * Time: 9:57 PM
 *
 * File: ConsistencyTrait.php
 */

Trait ConsistencyTrait
{

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function checkRelations($relationList)
    {
        if (!$relationList) {
            return true;
        }

        foreach ($relationList as $entry) {
            if (!class_exists($entry['model'])) {
                throw new QueryBuilderException($entry['model'] . ' Relations Class does not exist!');
            }
        }

        return true;
    }


}