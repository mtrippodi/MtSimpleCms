<?php
/**
 * @author    Michael Trippodi <mail@trippodi.com>
 * @license   New BSD License
 * 
 * Copyright (c) 2014, Michael Trippodi
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * 
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED 
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


namespace MtSimpleCms;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

use MtSimpleCms\ContentInterface;

class PagesTable extends AbstractTableGateway implements ContentInterface
{
    protected $table = 'pages';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->initialize();
    }
	
	
	public function getActivePage($content_key)
	{
		$result = $this->select(array('pg_key' => $content_key, 'isactive' => 1));
		return $result->current();
	}
    
	public function getHeadMeta($page_id)
	{
		$table = new \MtSimpleCms\PagesMetaTable($this->adapter);
		$result = $table->select(array('pgm_pg_id' => $page_id));
		
		$metas = array(
			'name' => array(),
			'property' => array()
		);
		
		foreach ($result as $row) {
			$metas[$row->pgm_attr][$row->pgm_attr_txt] = $row->pgm_content;
		}

		return $metas;
	}
}
