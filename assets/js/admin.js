jQuery(document).ready(function($) {

    jQuery('.rhmd-nav-tabs > li > a').click(function(event){
        event.preventDefault();
        //get displaying tab content jQuery selector
        var active_tab_selector = $('.rhmd-nav-tabs > li > a.current').attr('href');

        //find actived navigation and remove 'active' css
        var actived_nav = $('.rhmd-nav-tabs > li > a.current');
        actived_nav.removeClass('current');

        //add 'active' css into clicked navigation
        $(this).addClass('current');

        //hide displaying tab content
        $(active_tab_selector).removeClass('rh-active');
        $(active_tab_selector).addClass('rh-hide');

        var target_tab_selector = $(this).attr('href');
        $(target_tab_selector).removeClass('rh-hide');
        $(target_tab_selector).addClass('rh-active');
    });

    jQuery("#gpi_featured > .submit").click(function() {
        var data = {'action':'save_gpi'};
        $('ul[id=gpi_featured]').find('input').each(function () {
            data[this.id] = this.value;
        });
        $('ul[id=gpi_featured]').find('select').each(function () {
            data[this.id] = this.value;
        });
        data.nonce = RH.nonce;

        jQuery.post(ajaxurl, data, function(response) {
            var data = response.data,
                code = $('#hiddenCode code'),
                code_html = '[gpi client_id="' + data.client_id + '" url="'+data.url+'" prefill_text="'+data.txt+'" cta_label="'+data.cta+'" cta_url="'+data.ctaurl+'" button_text="'+data.btntxt+'"]';
            code.html(code_html);
            var btn = $('#myBtn');
            btn.text(data.btntxt);
            btn.prop('data-calltoactionlabel', data.cta);
            btn.prop('data-contenturl', data.url);
            btn.prop('data-calltoactionurl', data.ctaurl);
            btn.prop('data-prefilltext', data.txt);
        });
    });
});