<?php

/*
The plan is this:
create a noun, verb, adjective and adverb
create a collection
add_many the noun, verb etc as collection_items to this collection
save an instance of the collection, recursively
*/
class instanceTest extends \PHPUnit\Framework\TestCase
{
	public function test_class_exist()
    {
    	$status = class_exists('\kronos\instance');

        $this->assertEquals(true, $status);

        return $status;
    }

    /**
     * @depends test_class_exist
     */
    public function test_create_noun_success($status)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'noun',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'name' => 'A noun'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode
        );

        $this->assertEquals(200, $status['code']);

        $noun_id = $status['data'];
        return $noun_id;
    }

    /**
     * @depends test_class_exist
     */
    public function test_create_verb_success($status)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'verb',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'name' => 'A verb'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode
        );

        $this->assertEquals(200, $status['code']);

        $verb_id = $status['data'];
        return $verb_id;
    }

    /**
     * @depends test_class_exist
     */
    public function test_create_adjective_success($status)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'adjective',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'name' => 'An adjective'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode
        );

        $this->assertEquals(200, $status['code']);

        $adjective_id = $status['data'];
        return $adjective_id;
    }

    /**
     * @depends test_class_exist
     */
    public function test_create_adverb_success($status)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'adverb',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'name' => 'An adverb'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode
        );

        $this->assertEquals(200, $status['code']);

        $adverb_id = $status['data'];
        return $adverb_id;
    }

    /**
     * @depends test_class_exist
     */
    public function test_create_collection_success($status)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'collection',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'name' => 'A collection of the four: noun, verb, adjective and adverb'
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode
        );

        $this->assertEquals(200, $status['code']);

        $collection_id = $status['data'];
        return $collection_id;
    }

    /**
     * @depends test_create_collection_success
     * @depends test_create_noun_success
     * @depends test_create_verb_success
     * @depends test_create_adjective_success
     * @depends test_create_adverb_success
     */
    public function test_collection_item_add_many_success($collection_id, $noun_id, $verb_id, $adjective_id, $adverb_id)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'collection_item',
                'action' => 'add_many',
                'url_parameters' => array(),
                'params' => array(
                    'collection_id' => $collection_id,
                    'type' => ['noun', 'verb', 'adjective', 'adverb'],
                    'model' => [$noun_id, $verb_id, $adjective_id, $adverb_id]
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode
        );

        $this->assertEquals(200, $status['code']);

        $collection_item_ids = $status['data'];
        return $collection_item_ids;
    }

    /**
	 * @depends test_create_collection_success
     */
	public function test_create_rec_0_success($collection_id)
    {
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'instance',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
            		'type' => 'collection',
                    'parent' => $collection_id,
                    'value' => 'The value of a non-recursive collection instance is quite useless.',
                    'recursion' => 0
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

    	$this->assertEquals(200, $status['code']);

        $id = $status['data'];
        return $id;
    }

    /**
     * @depends test_create_collection_success
     * @depends test_collection_item_add_many_success
     */
    public function test_create_rec_1_success($collection_id, $collection_item_ids)
    {
        //if all goes well, collection_item_ids are noun -> adverb

        $item_dict = array();
        for($i = 0; $i < 4; $i++)
        {
            switch ($i) {
                case 0:
                    $item_dict[$collection_item_ids[$i]] = 'Noun value';
                break;

                case 1:
                    $item_dict[$collection_item_ids[$i]] = 'Verb value';
                break;

                case 2:
                    $item_dict[$collection_item_ids[$i]] = 'Adjective value';
                break;

                case 3:
                    $item_dict[$collection_item_ids[$i]] = 'Adverb value';
                break;
            }
            
        }

        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'instance',
                'action' => 'create',
                'url_parameters' => array(),
                'params' => array(
                    'type' => 'collection',
                    'parent' => $collection_id,
                    'value' => $item_dict,
                    'recursion' => 1
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
            $GLOBALS['APP'],
            $model_profile,
            $request,
            true //test mode
        );

        $this->assertEquals(200, $status['code']);

        $id = $status['data'];
        return $id;
    }

    /**
	 * @depends test_create_success
     */
    public function txst_read_rec_0_success($id)
	{
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'instance',
                'action' => 'read',
                'url_parameters' => array(),
                'params' => array(
            		'_id' => $id,
                    'recursion' => 0
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            count($status['data']) == 1) AND
            ($status['data'][0]['_id'] == $id)
        );
	}


	//read all
	public function txst_read_all_success()
	{
		$request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'instance',
                'action' => 'read_all',
                'url_parameters' => array(),
                'params' => array(
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            count($status['data']) == 1)
        );
	}

    /**
	 * @depends test_create_success
     */
    public function txst_update_rec_0_success($id)
	{
        $request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'instance',
                'action' => 'update',
                'url_parameters' => array(),
                'params' => array(
            		'_id' => $id,
                    'type' => 'noun',
                    'parent' => '5aec74b2363ab8180c50ee90',
                    'value' => 'Stephen stigwue Igwue',
                    'recursion' => 0
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            ($status['message'] == true))
        );
	}

    /**
	 * @depends test_create_success
     */
    public function txst_delete_rec_0_success($id)
	{
		$request = array(
            'code' => 200,
            'message' => '',
            'data' => array(
                'model' => 'instance',
                'action' => 'delete',
                'url_parameters' => array(),
                'params' => array(
            		'_id' => $id,
                    'recursion' => 0
                )
            )
        );

        $model_profile = \iriki\engine\route::buildModelProfile($GLOBALS['APP'], $request);

        //handle the request: match a route to a model and its action
        $status = \iriki\engine\route::matchRequestToModel(
        	$GLOBALS['APP'],
        	$model_profile,
        	$request,
			true //test mode
        );

        $this->assertEquals(true,
            (($status['code'] == 200) AND
            ($status['message'] == true))
        );

	}

}

?>
