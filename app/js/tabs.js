/*--- tabs ---*/
function initTabs(tabsContainer, tabsList, tabItem)
{
    $(tabsContainer).each(function(){

        var _hold = $(this);
        var _btn = _hold.find(tabsList);
        var _box = _hold.find(tabItem);

        var _a = _btn.index(_btn.filter('.active:eq(0)'));
        if(_a == -1) _a = 0;
        _btn.removeClass('active').eq(_a).addClass('active');
        _box.removeClass('active').css({
            display:'none'
        });
        _box.eq(_a).addClass('active').css({
            display:'block'
        });

        _btn.click(function(){
            changeTab(_btn.index(this));
            return false;
        });

        if(window.location.hash) changeTab(window.location.hash);

        function changeTab(_ind){
            if(typeof _ind != 'number'){
                var i=_btn.index(_btn.find('a[href="'+_ind+'"]').parent());
                if(i == -1) return; else _ind=i;
            }
            if(_ind != _a){
                _btn.eq(_a).removeClass('active');
                _btn.eq(_ind).addClass('active');
                _box.eq(_a).removeClass('active').css({
                    display:'none'
                });
                _box.eq(_ind).addClass('active').css({
                    display:'block'
                });
                _a = _ind;
            }
        }
    });
}
$(document).ready(function(){
    initTabs('div.tabs-wrap', '.tabs-nav li', 'div.tabs-content div.tab-box');
});