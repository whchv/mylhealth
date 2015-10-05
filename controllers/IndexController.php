<?php

class IndexController extends Common {

	public function __construct() {
        parent::__construct();
	}
	
	public function indexAction() {
		if (file_exists(APP_ROOT . 'cache/index/' . $this->siteid . '.html')) {
			echo file_get_contents(APP_ROOT . 'cache/index/' . $this->siteid . '.html');
			exit;
		}
		
	    $this->view->assign(array(
			'page'             => 'index',
			'indexc'           => 1, //首页标识符
			'meta_title'       => $this->site['SITE_TITLE'],
			'meta_keywords'    => $this->site['SITE_KEYWORDS'],
			'meta_description' => $this->site['SITE_DESCRIPTION'],
			'members'          => array('1', '2'),
	    ));
		$this->view->display('index');
	}
	
	public function dongtaiAction() {
		$type = $this     ->get('tt') ;
		$cid  = (int)$this->get('cc') ;
		$uid  = (int)$this->get('uu') ;
		$path = array() ;
		if($cid){
			$category = $this->model('category')->find($cid) ;
		}
		if($uid){
			$member = $this->model('member')->find($uid) ;
		}

		switch($type){
			case 'category' :
			    // 页面路径
				array_push($path, array(
					'catid' => $category['catid'],
					'name'  => $category['catname'], 
					'url'   => SITE_URL. 'index.php?a=dongtai&tt=category&cc='. $category['catid'])
				) ;
				break ;

			case 'member' :
			    // 页面路径
				array_push($path, array(
					'userid' => $member['id'],
					'catid'  => $category['cid'],
					'name'   => $member['nickname'], 
					'url'    => SITE_URL. 'index.php?a=dongtai&tt=member&uu='. $member['id'])
				) ;

				$sql = "select * from tb_member a, tb_member_geren b where a.id=b.id and a.id=$uid" ;
				$member = $this->model('category')->execute($sql, false) ;
				break ;
		}

		// 动态统计
		$sql = "select a.catid, a.catname, case when b.catid is null then 0 else count(b.catid) end cnt
				from tb_category a left outer join tb_content_1 b 
				on (a.catid = b.catid and b.status = 1 and b.userid = $uid) 
				where a.parentid = 1 group by a.catid, a.catname order by 1 " ;
		$stats = $this->model('category')->execute($sql) ;
   
		// 模板变量定义
	    $this->view->assign(array(
			'page'             => 'dongtai',
			'type'             => $type,
			'id'               => $id,
			'path'             => $path,
			'stats'            => $stats,
			'member'           => $member,
			'category'         => $category,
			'meta_title'       => '最新动态 · '. $path[0]['name']. ' · '. $this->site['SITE_TITLE'],
			'meta_keywords'    => $this->site['SITE_KEYWORDS'],
			'meta_description' => $this->site['SITE_DESCRIPTION'],
	    ));

		$this->view->display('dongtai') ;
	}
	
	// 动态内容
	public function contentAction() {
		$type  = $this     ->get('tt') ;
		$cid   = (int)$this->get('cc') ;
		$model = $this     ->model('member') ;
		$path  = array() ;

		// 内容信息关联
		$sql = "select a.id contentid, a.title, b.content, a.userid, a.catid, c.catname, 
				from_unixtime(a.updatetime, '%Y-%m-%d') updatetime, d.nickname, e.xianshi, a.sysadd, f.realname 
				from tb_content_1 a, tb_content_1_news b, tb_category c, tb_member d, tb_member_geren e, tb_user f 
				where a.id = b.id and a.catid = c.catid and a.userid = d.id and a.userid = e.id and a.sysadd = f.site and a.id = $cid " ;
		$content = $this->model('content')->execute($sql, false) ;

		/************************ 动态内容生成。。不明觉厉 ************************/
		$data  = $this->content->find($cid);	 //查询当前文档数据
		$model = $this->get_model();	     //获取模型缓存
		$catid = $data['catid'];	         //赋值栏目id
		$cat   = $this->cats[$catid];	     //获取当前文档的栏目数据
		$table = $model[$data['modelid']]['tablename'];
		$_data = $this->db->where('id', $cid)->get($table)->row_array();	//附表数据查询
		$data  = array_merge($data, $_data);                            //合并主表和附表
		$data  = $this->getFieldData($model[$cat['modelid']], $data);	//格式化部分数据类型（这个是重点）
		$data  = $this->get_content_page($data, 1, $page);	            //内容分页和子标题
		$content['content'] = $data['content'] ;
		/************************ 动态内容生成。。不明觉厉 ************************/
		
		switch($type){
			case 'category' :
				$path = array(
					array('name'=>$content['title'], 'url'=>SITE_URL. 'index.php?a=content&tt=category&cc='. $content['contentid']),
					array('name'=>$content['catname'], 'url'=>SITE_URL. 'index.php?a=dongtai&tt=category&cc='. $content['catid']),
				) ;

				$uid = 0 ;
				$and_index = 'and userid=0 and catid = '. $content['catid'] ;
				break ;

			case 'member' :
				$path = array(
					array('name'=>$content['title'], 'url'=>SITE_URL. 'index.php?a=content&tt=member&cc='. $content['contentid']),
					array('name'=>$content['nickname'], 'url'=>SITE_URL. 'index.php?a=dongtai&tt=member&uu='. $content['userid']),
				) ;
				
				$uid = $content['userid'] ;
				$and_stats = $and_index = "and userid = ". $content['userid'] ;
				break ;
		}

	    // 动态分类统计
		$sql = "select a.catid, a.catname, case when b.catid is null then 0 else count(b.catid) end cnt
				from tb_category a left outer join tb_content_1 b 
				on (a.catid = b.catid and b.status = 1 and b.userid = $uid) 
				where a.parentid = 1 group by a.catid, a.catname order by 1 " ;
		$stats = $this->model('category')->execute($sql) ;
   

		// 翻页设置（目标定位）
		$sql = "select count(1) cnt from tb_content_1 where status=1 $and_index and updatetime < ". $data['updatetime'] ;
		$cnt = $this->model('category')->execute($sql, false) ;
		$cnt = $cnt['cnt'] ;

		// 翻页设置（上一篇）
		if($cnt){
			$sql = "select id, title from tb_content_1 
					where status=1 $and_index order by updatetime limit ". (string)($cnt-1) . ', 1' ;
			$before = $this->model('category')->execute($sql, false) ;
		}

		// 翻页设置（下一篇）
		$sql = "select id, title from tb_content_1 
				where status=1 $and_index order by updatetime limit ". (string)($cnt+1) . ', 1' ;    
		$after = $this->model('category')->execute($sql, false) ;

		// 模板变量定义
	    $this->view->assign(array(
			'page'       => 'content',
			'type'       => $type,
			'cid'        => $cid,
			'path'       => $path,
			'stats'      => $stats,
			'content'    => $content, 
			'before'     => $before, 
			'after'      => $after, 
			'meta_title' => $content['catname']. '动态 · '. $content['title']. ' · '. $this->site['SITE_TITLE'],
	    ));

		$this->view->display('content') ;
	}
	
	public function chanpinAction() {
		$type = $this ->get('tt') ;

		(int)$this->get('cc') ? $cid = (int)$this->get('cc') : $cid = 6 ;

		$path     = array(array('name'=>'产品', 'url'=>SITE_URL)) ;
		$category = $this->model('category')->where('parentid=5')->select() ;

		for($i = 0; $i < count($category); $i++){
			$category[$i]['item'] = $this->model('content')->where('catid='. $category[$i]['catid']. ' and status=1')->order('listorder')->select() ;
		}

		if($type == 'sub'){
			/************************ 动态内容生成。。不明觉厉 ************************/
			$data    = $this->content->find($cid);   //查询当前文档数据
			$model   = $this->get_model();	         //获取模型缓存
			$catid   = $data['catid'];	             //赋值栏目id
			$cat     = $this->cats[$catid];	         //获取当前文档的栏目数据
			$table   = $model[$data['modelid']]['tablename'];
			$_data   = $this->db->where('id', $cid)->get($table)->row_array();	//附表数据查询
			$data    = array_merge($data, $_data);                              //合并主表和附表
			$data    = $this->getFieldData($model[$cat['modelid']], $data);	    //格式化部分数据类型（这个是重点）
			$data    = $this->get_content_page($data, 1, $page);	            //内容分页和子标题
			$chanpin = $data ;
			/************************ 动态内容生成。。不明觉厉 ************************/

			$path = array(
				array('name'=>$data['title'], 'url'=>SITE_URL. 'index.php?a=chanpin&tt=sub&cc='. $data['id']),
				array('name'=>'产品',         'url'=>SITE_URL),
			) ;
		}

	    $this->view->assign(array(
			'path'             => $path,
			'page'             => 'chanpin_'. $type,
			'cid'              => $cid,
			'chanpin'          => $chanpin,
			'category'         => $category,
			'meta_title'       => $path[0]['name']. '· '. $this->site['SITE_TITLE'],
			'meta_keywords'    => $this->site['SITE_KEYWORDS'],
			'meta_description' => $this->site['SITE_DESCRIPTION'],
	    ));
	    
	    $this->view->display('chanpin_'. $type) ;
	}
	
	public function aboutAction() {
		$cid = (int)$this->get('cc') ;

		if($cid){  // 有ID直接取目标信息
			$cat = $this->model('category')->find($cid) ;
		}else{     // 无ID取总栏目下第一个子栏目信息
			$sql = "select a.* from tb_category a, tb_category b 
					where a.parentid = b.catid and b.catname = '关于' order by a.listorder" ;
			$cat = $this->model('category')->execute($sql, false) ;
		}

		// 目标栏目信息
		$path = array(
			array('name'=>$cat['catname'], 'url'=>SITE_URL. 'index.php?a=about&cc='. $cat['catid']),
		) ;

		// 所有同级栏目信息
		$category = $this->model('category')->where('parentid='. $cat['parentid'])->order('listorder')->select() ;

		for($i = 0; $i < count($category); $i++){
			$model        = $this->get_model() ;
			$data         = $model[$this->cats[$category[$i]['catid']]['modelid']] ;
			$data         = $this->getFieldData($data, $category[$i]);  // 格式化部分数据类型（重点）
			$category[$i] = $this->get_content_page($data, 1, $page) ;
		}

	    $this->view->assign(array(
			'page'             => 'about',
			'path'             => $path,
			'data'             => $category,
			'cid'              => $cat['catid'],
			'meta_title'       => '关于贝舒乐 · '. $this->site['SITE_TITLE'],
			'meta_keywords'    => $this->site['SITE_KEYWORDS'],
			'meta_description' => $this->site['SITE_DESCRIPTION'],
	    ));

		$this->view->display('about') ;
	}
	
	public function postAction() {
		$page  = $this->post('page') ;
		$num   = $this->post('num') ;
		$where = $this->post('where') ;

	    // 默认数据量设置
	    $num ? $num : $num = 20 ;
	    $where ? $and = " and a.$where " : $and = " and 1 = 1 " ;

		$limit = "limit ". $page * $num. ", $num" ;
		$next  = "limit ". ($page + 1) * $num. ", 1" ;
		
		// 内容信息关联清单
		$sql = "select a.id, a.title, from_unixtime(a.updatetime, '%Y-%m-%d') date, b.catid, b.catname 
				from tb_content_1 a, tb_category b 
				where a.catid = b.catid and a.status= 1 $and order by a.updatetime desc $limit " ;
		$data['list'] = $this->model('content')->execute($sql) ;

		// next?
		$sql = "select * from tb_content_1 a where status = 1 $and order by updatetime desc $next " ;
		$data['next'] = $this->model('content')->execute($sql, false) ;

		if(!$data['next']){
			$data['next'] = false ;
		}

		echo json_encode($data)  ;
	}
}