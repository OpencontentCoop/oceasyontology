{ezpagedata_set( 'has_container', true() )}

{if $error}
    <div class="container">
        <div class="message-error">
            <p>{$error}</p>
        </div>
    </div>
{else}
<div class="p-3 u-padding-all-xl">
    <h1 data-uri="{$uri|wash()}">{$uri|wash()}</h1>
    <div class="row mt-5 Grid Grid--withGutter u-margin-top-xl">
        <div class="col-md-2 Grid-cell u-md-size1of4 u-lg-size1of6">
            <ul class="nav nav-pills">
                {foreach $output_format_list as $name => $value}
                    <li role="presentation"
                        class="nav-item w-100 u-margin-bottom-l">
                        <a class="text-decoration-none nav-link{if $value|eq('turtle')} active{/if}"
                           data-value="{$value}"
                           data-header="{$header_format_list[$name][0]}"
                           data-available_headers="{$header_format_list[$name]|implode(',')}"
                           href="#">
                            {$name|wash()}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
        <div class="col-md-10 Grid-cell u-md-size3of4 u-lg-size5of6">
            <pre class="response" style="font-size: .8em"></pre>
        </div>
    </div>
</div>
{/if}


{ezscript_require(array('ezjsc::jquery'))}
<script>{literal}
    $(document).ready(function () {
        var loadData = function () {
            var uri = $('[data-uri]').data('uri');
            var header = $('.nav-pills a.active').data('header');
            var request = $.ajax({
                url: uri,
                method: "GET",
                data: {encode: true},
                headers: {
                    Accept: header
                },
                success: function (response) {
                    try {
                        var json = JSON.parse(response);
                        response = JSON.stringify(json, undefined, 2);
                    } catch (e) {
                        console.log(e);
                    }
                    $('pre.response').html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });
        };
        $('.nav-pills a').on('click', function (e) {
            $('.nav-pills a.active').removeClass('active');
            $(this).addClass('active');
            e.preventDefault();
            loadData();
        });
        loadData();
    });
    {/literal}</script>