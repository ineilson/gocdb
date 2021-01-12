<?php
namespace org\gocdb\services;
/* Copyright (c) 2011 STFC
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * GOCDB Stateless service facade (business routines) for group objects.
 * The public API methods are transactional.
 *
 * @author Ian Neilson after originals -
 * @author John Casson
 * @author David Meredith
 * @author George Ryall
 */

require_once __DIR__ . '/AbstractEntityService.php';
require_once __DIR__.  '/../Doctrine/entities/APIAuthentication.php';

use Doctrine\ORM\QueryBuilder;

class APIAuthenticationService extends AbstractEntityService{

    function __construct() {
        parent::__construct();
    }

    /**
     * Returns the APIAuthentication entity associated with the given identifier.
     * @param string $ident Identifier (e.g. X.509 DN as string)
     * @param string $type  Identifyer type (e.g. "X509")
     * @return \APIAuthentication APIAuthentication associated with this identifier
     */
    public function getAPIAuthentication($ident, $type) {

        if (!is_string($ident)) {
            throw new \LogicException("Expected string APIAuthentication identifier.");
        }

        $dql = "SELECT a FROM APIAuthentication a " .
                "WHERE (a.identifier = :ident AND a.type = :type)" ;


        $qry = $this->em->createQuery($dql);
        $qry->setParameter('ident', $ident);
        $qry->setParameter('type', $type);

        $apiAuth = $qry->getOneOrNullResult();

        return $apiAuth;
    }
}
