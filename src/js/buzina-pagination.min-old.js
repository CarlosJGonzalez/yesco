/**
 * Buzina Pagination http://github.com/mikebrsv/buzina-pagination
 * Author: Mikhail Borisov http://github.com/mikebrsv
 * License: https://github.com/mikebrsv/buzina-pagination/blob/master/LICENSE
 */

(function($){$.fn.buzinaPagination=function(options){var settings=$.extend({prevnext:!0,prevText:"Previous",nextText:"Next",itemsOnPage:1},options);var buzinaContent=$(this);var pagesTotal=buzinaContent.children().length;var buzinaContentId=buzinaContent.attr("id");var pageClass=buzinaContentId+"--page-";var pagerId=buzinaContentId+"--pager";for(i=0;i<pagesTotal;i++){$("#"+buzinaContentId+"> div").slice(i,i+settings.itemsOnPage).wrapAll("<div></div>");buzinaContent.children(":eq("+i+")").addClass(pageClass+(i+1)+" content-page")}
pagesTotal=buzinaContent.children().length;buzinaContent.children(":first").addClass("content-page-active");var pagerDom=createPagerDom(pagesTotal,pagerId,settings);buzinaContent.after(pagerDom);$("#"+pagerId+" a").click(function(e){e.preventDefault();var pageClicked=this.text;if(pageClicked==settings.prevText){var currentActive=$(".content-page-active").attr("class");console.log(currentActive);var currentActiveNumber=currentActive.substring(currentActive.indexOf("--page")+7,currentActive.indexOf(" "));if(currentActiveNumber>1){$(".content-page").removeClass("content-page-active");$("."+pageClass+(parseInt(currentActiveNumber)-1)).addClass("content-page-active")}}else if(pageClicked==settings.nextText){var currentActive=$(".content-page-active").attr("class");var currentActiveNumber=currentActive.substring(currentActive.indexOf("--page")+7,currentActive.indexOf(" "));if(currentActiveNumber<pagesTotal){$(".content-page").removeClass("content-page-active");$("."+pageClass+(parseInt(currentActiveNumber)+1)).addClass("content-page-active")}}else{$(".content-page").removeClass("content-page-active");$("."+pageClass+pageClicked).addClass("content-page-active")}})}})(jQuery);
function createPagerDom(pagesTotal,pagerId,settings){ 
	var pagerConc="";for(i=0;i<pagesTotal;i++){pagerConc+=`
      <li class="page-item">
        <a class="page-link text-dark" href="#">${i + 1}</a>
      </li>`}
if(settings.prevnext){var prevDom=`
    <li class="page-item">
      <a class="page-link text-dark" href="#">${settings.prevText}</a>
    </li>`;var nextDom=`
    <li class="page-item">
      <a class="page-link text-dark" href="#">${settings.nextText}</a>
    </li>
    `;pagerConc=prevDom+pagerConc+nextDom}
return `
    <nav id="${pagerId}">
      <ul class="pagination justify-content-center">        
        ${pagerConc}        
      </ul>
    </nav>`}

