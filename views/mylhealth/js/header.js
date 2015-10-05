
// //输入框自动补充
// $(function(){
// 	$("#SearchKey").focus();
// 	$("input[name='searchkw']").unautocomplete();
//     $("input[name='searchkw']").autocomplete(sitepath+'?c=api&a=search',{
// 		minChars: 1,
// 		width: 346,
// 		matchContains: true,
// 		autoFill: false,
// 		resultsClass :'search_input' ,
// 		matchCase:false,
// 		selectFirst: false,
// 		formatItem:function(row,i,max){
// 			return row[0];
// 		}
// 	}).result(function(event, data, formatted){
// 		formatted=formatted.replace(/<[\s\S]*?>/ig, "");
// 		$("input[name='searchkw']").val(formatted);
// 	});
// });

// //提交搜索表单
// function search_post() {
//    var kw=$('#SearchKey').val();
//    if (kw) {
// 	   var url=sitepath+'?c=content&a=search&kw='+kw;
// 	   window.location.href=url;
// 	   return false;
//    } else {
//       return false;
//    }
// }

// //会员收藏夹
// function addfavorite(id, obj) {
//     $('#'+obj).html('处理中');
// 	$.post(sitepath+'?c=api&a=addfavorite&r='+Math.random(), { id:id }, function(data){
// 		$('#'+obj).html(data); 
// 	});
// }


/************************************************************************/

//输入框自动补充
$(function(){
	// $('#adCarousel').carousel() ;  // 首页图片轮播

	// 手动加载更多动态列表
	// $('button.dongtai-more').click(function(){
	// 	var $clone,
	// 		$this   = $(this),
	// 	    $parent = $(this).parent(),
	// 		$tpl    = $parent.find('.tpl'),
	// 		$list   = $parent.find('.dongtai-list'),
	// 		page    = $parent.attr('data-page'),
	// 		num     = $parent.attr('data-num'),
	// 		where   = $parent.attr('data-where') ;

 //        $.ajax({
 //            type    : 'post',
 //            url     : $(this).attr('data-post'), 
 //            data    : {page:page, num:num, where:where},
 //            success : function(data$){
 //        		var data$ = $.evalJSON(data$),
 //        		    list$ = data$['list'],
 //        		    next  = data$['next'] ;

 //        		for(var i = 0; i < list$.length; i++){
 //        			$clone = $tpl.clone().removeClass('tpl').show() ;

 //        			$clone.find('.dt-date').text(list$[i]['date']) ;
 //        			$clone.find('.dt-type>a').text(list$[i]['catname'])
 //        				  .attr('href', $clone.find('.dt-type>a').attr('href')+list$[i]['catid']) ;
 //        			$clone.find('.dt-title>a').text(list$[i]['title'])
 //        				  .attr('href', $clone.find('.dt-title>a').attr('href')+list$[i]['id']) ;


 //        			$list.append($clone) ;
 //        		}

 //        		if(next){
 //        			$parent.attr('data-page', parseInt($parent.attr('data-page')) + 1) ;
 //        		}else{
 //        			$this.hide() ;
 //        		}
 //            }
 //        });
	// }) ;
});
