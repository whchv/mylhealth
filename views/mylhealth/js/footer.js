

//输入框自动补充
$(function(){
    // 首页图片轮播
	$('#adCarousel').carousel() ;

    // 最近三天动态区分
    $('.dt-list').each(function(){
        if(!$(this).hasClass('tpl')){
            if(moment().diff(moment($(this).find('.dt-date').text(), 'YYYY-MM-DD'), 'days') < 3){
                $(this).addClass('hot') ;
            }
        }
    }) ;

    // 动态列表初始判断
    if(!$('.dt-list').not('.tpl').size()){
        $('button.dongtai-more').attr('disabled', true).text('暂无动态') ;
    }else if($('.dt-list').not('.tpl').size() < parseInt($('.dong-tai').attr('data-num'))){
        $('button.dongtai-more').hide() ;
    }

    // 底部栏微信号查看交互
    $.each($('.weixin a'), function(){
        $(this).popover({
            html      : true,
            placement : 'top',
            trigger   : 'hover',
            content   : $('.footer-tpl .'+ $(this).attr('data-img')),
        }) ;
    }) ;

    // 个人背景换行处理
    $('.beijing>.box-body').html($('.beijing>.box-body').text().replace(/\n/g,'<br/>')) ;   

	// 手动加载更多动态列表
	$('button.dongtai-more').click(function(){
		var $clone,
			$this   = $(this),
		    $parent = $(this).parent(),
			$tpl    = $parent.find('.tpl'),
			$list   = $parent.find('.dongtai-list'),
			page    = $parent.attr('data-page'),
			num     = $parent.attr('data-num'),
			where   = $parent.attr('data-where') ;

        $.ajax({
            type    : 'post',
            url     : $(this).attr('data-post'), 
            data    : {page:page, num:num, where:where},
            success : function(data$){
        		var data$ = $.evalJSON(data$),
        		    list$ = data$['list'],
        		    next  = data$['next'] ;

        		for(var i = 0; i < list$.length; i++){
        			$clone = $tpl.clone().removeClass('tpl').show() ;

        			$clone.find('.dt-date').text(list$[i]['date']) ;
        			$clone.find('.dt-type>a').text(list$[i]['catname'])
        				  .attr('href', $clone.find('.dt-type>a').attr('href')+list$[i]['catid']) ;
        			$clone.find('.dt-title>a').text(list$[i]['title'])
        				  .attr('href', $clone.find('.dt-title>a').attr('href')+list$[i]['id']) ;

        			$list.append($clone) ;
        		}

        		if(next){
        			$parent.attr('data-page', parseInt($parent.attr('data-page')) + 1) ;
        		}else{
        			$this.hide() ;
        		}
            }
        });
	}) ;
});
