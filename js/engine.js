// путь к папке, где установлена галлерея
var SUBDIR = "";

function replacel(what, by, div, n){
    (!n) ? n = 1 : n = n;
    what = what.split(div);
    what[what.length - n] = by;
    return what.join(div);
}

function loadpreview(items){
    items.each(function(){
        var ref = $(this).attr('href').replace('#', '');
        var img = $(this).children('img');
        if (!img.attr('src')) {
            $.post(SUBDIR + 'engine/api.php', {
                action: 'thumbnail',
                path: ref,
                size: 'small'
            }, function(data){
                img.attr('src', data);
            });
        }
    });
}

function loadc(hash){
    (!hash) ? hash = '/' : hash = hash.replace('#', '');
    
    $.get(SUBDIR + 'ajax.php', {
        path: hash
    }, function(data){
        $('.fgallery').html(data);
        $('.folders-loading').addClass('folders');
        //делаем так, чтобы список превью можно было крутить колесиком
        if ($(".files").size()) {
            $("div.files").scrollable({
                onSeek: function(){
                    loadpreview(this.getVisibleItems());
                }
            }).mousewheel();
            var api = $("div.files").scrollable({
                api: true
            });
            //когда загружается страница с картинкой делаем так, чтобы в список прокрутился до превью картинки
            api.seekTo($(".items a").index($(".items a.active")) - 2);
            loadpreview(api.getVisibleItems());
        }
        
        $('.image-info').css('opacity', 0.6);
        
        $('.image').mouseenter(function(){
            $('.image-info').animate({
                'opacity': 0.9
            }, 100);
        }).mouseleave(function(){
            $('.image-info').animate({
                'opacity': 0.6
            }, 100);
        });
        
        $('.image a:first').colorbox();
        
        $('div.items p').css('opacity', 0);
        
        $('div.items a').mouseenter(function(){
            $(this).children('p').animate({
                'opacity': 0.9
            }, 100);
        }).mouseleave(function(){
            $(this).children('p').animate({
                'opacity': 0
            }, 100);
        });
        
        $('.folders a').click(function(){
            $('.folders').addClass('folders-loading');
            loadc($(this).attr('href').replace('#', ''));
        });
        
        $('.rotate-left, .rotate-right').click(function(){
            var cl = $(this).attr("class");
            var angle = 90;
            switch (cl) {
                case 'rotate-left':
                    angle = 90;
                    break;
                case "rotate-right":
                    angle = 270;
                    break;
            }
            
            var ref = $(".items a.active").attr("href").replace('#', '');
            
            $.post(SUBDIR + 'engine/api.php', {
                action: 'rotateThumbnail',
                path: ref,
                angle: angle
            });
            return false;
        });
    });
    return false;
}

$(document).ready(function(){
    //$.history.init(loadc);
    loadc(document.location.hash.replace('#', ''));
    
    // событие при клике на превью
    $('.files .items img').live('click', function(){
        $('.items a.active').removeClass('active');
        
		ref = $(this).parent('a').attr('class', 'active').attr('href');
        ref = ref.replace('#', '');
        
        $('#big-preview').animate({
            opacity: 0.6
        }, 200);
        
        // отправляем запрос на получение адреса превью
        $.post(SUBDIR + 'engine/api.php', {
            action: 'thumbnail',
            path: ref,
            size: 'big'
        }, function(data){
            $('#big-preview').attr('src', data).animate({
                opacity: 1
            }, 200);
            
            //достаем название файла
            fname = ref.split('/');
            fname = fname[fname.length - 1];

            //заменим в адресе последнюю часть
            infoRef = replacel($('.image-info a').attr('href'), fname, '/');
            
            //заменяем ссылку на новую
            $('.image-info a').attr('href', infoRef);
            
            $.post(SUBDIR + 'engine/api.php', {
                action: 'thumbnail',
                path: ref,
                size: 'lightbox'
            }, function(data){
                t = replacel($('#big-preview').attr('src'), fname, '/');
                
                // получаем позицию элемента, который равен 'small', чтобы заменить потом на 'lightbox'
                t = t.split('/');
                for (var i = 0; i < t.length; i++) {
                    if (t[i] == 'big') 
                        t[i] = 'lightbox';
                }
                t = t.join('/');
                $('#big-preview').parent('a').attr('href', t);
            });
            
        });
    });
});
