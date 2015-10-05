<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.5.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */
	
class Wx extends Admin {

    public $file;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->file = FCPATH.'config/weixin.php';
    }
	
	/**
     * 配置
     */
    public function index() {

		if (IS_POST) {
			$data = $this->input->post('data');
			$size = file_put_contents($this->file, array2string($data));
			if (!$size) {
                $this->adminMsg('config目录无权限写入');
            }
			$this->adminMsg('保存成功', url('admin/wx/index'), 3, 1, 1);
		}

		$this->template->assign(array(
			'data' => is_file($this->file) ? string2array(file_get_contents($this->file)) : array(),
		));
		$this->template->display('admin/wx_index.html');
    }
	
	
}