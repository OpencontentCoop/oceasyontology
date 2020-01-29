<h2>
    {$collection.classSchema.name[$locale]|wash()} - {$map.slug|wash()}
    <a href="{concat('easyontology/mapper/', $collection.classIdentifier, '/', $map.slug)|ezurl(no)}"><img src={"edit.gif"|ezimage} alt="{'Edit'|i18n( 'easyontology/dashboard' )}" /></a>
</h2>

<table id="tab-locations-list" class="list" cellspacing="0">
    <tr>
        <th>{'Source'|i18n( 'easyontology/dashboard' )}</th>
        <th>{'Target'|i18n( 'easyontology/dashboard' )}</th>
    </tr>

    {foreach array('_class') as $field sequence array( bgdark, bglight ) as $sequence}
        <tr class="{$sequence}">
            <td>
                <p><strong>@type</strong></p>
            </td>
            <td>
                {if is_set($map.flat_mapping[$field])}
                    {foreach $map.flat_mapping[$field] as $uri}
                        <p>{$uri|wash()}</p>
                    {/foreach}
                {/if}
            </td>
        </tr>
    {/foreach}


    {foreach $collection.classSchema.fields as $field sequence array( bglight, bgdark ) as $sequence}
        <tr class="{$sequence}">
            <td>
                <p><strong>{$field.name[$locale]|wash()}</strong> - {$field.identifier}</p>
                <p><em>{$field.dataType|wash()}</em></p>
            </td>
            <td>
                {if is_set($map.flat_mapping[$field.identifier])}
                <table style="width: 100%">
                {foreach $map.flat_mapping[$field.identifier] as $uri}
                    <tr class="{$sequence}">
                        <td width="50%">{$uri|wash()}</td>
                        <td>
                            <dl>
                            {foreach $map.properties[$uri] as $key => $value}
                                <dt>{$key|wash()}</dt>
                                <dd>{$value|wash()}</dd>
                            {/foreach}
                                <dt>Converter</dt>
                                <dd>{easyontology_converter_name($map.properties[$uri], hash('datatype', $field.dataType))}</dd>
                            </dl>
                        </td>
                    </tr>
                {/foreach}
                </table>
                {/if}
            </tr>
        </tr>
    {/foreach}
</table>