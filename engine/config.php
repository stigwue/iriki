<?php

namespace iriki;

class config extends \iriki\engine\request
{
	public function create($request, $wrap = true)
	{
		return $request->create($request, $wrap);
	}

	public function read($request, $wrap = true)
	{
		return $request->read($request, $wrap);
	}

	public function read_by_key($request, $wrap = true)
	{
		$request->setParameterStatus([
			'final' => array('key'),
			'missing' => array(),
			'extra' => array(),
			'ids' => array()
		]);

		return $request->read($request, $wrap);
	}

	public function read_all($request, $wrap = true)
	{
		$request->setParameterStatus([
			'final' => array(),
			'missing' => array(),
			'extra' => array(),
			'ids' => array()
		]);

		return $request->read($request, $wrap);
	}

	public function read_key_dictionary($request, $wrap = true)
	{
		$request->setParameterStatus([
			'final' => array(),
			'missing' => array(),
			'extra' => array(),
			'ids' => array()
		]);

		$configs = $request->read($request, false);

		$dict = \iriki\engine\parser::dictify($configs, 'key', true);

		return \iriki\engine\response::data($dict, $wrap);
	}
}

?>