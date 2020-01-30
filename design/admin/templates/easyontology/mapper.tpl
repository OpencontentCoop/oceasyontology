{if is_set($error)}
    <div class="alert alert-danger">
        {$error|wash()}
    </div>
{else}
<form action="{concat('easyontology/mapper/', $collection.classIdentifier, '/', $map.slug)|ezurl(no)}" method="post">
    <h2>{'Select ontology from library'|i18n( 'easyontology/dashboard', '', hash('%class_name%', $class.name|wash()) )}</h2>
    <div class="block float-break clearfix">
        <label for="ontology" class="hidden hide">{'Ontology list'|i18n( 'easyontology/dashboard' )}</label>
        <select name="ontology" id="ontology">
            <option></option>
            {foreach $ontologies as $ontology}
                {if $map.ontologies|contains($ontology.uri)|not()}
                    <option value="{$ontology.slug|wash()}">{$ontology.uri|wash()}</option>
                {/if}
            {/foreach}
        </select>
        <input type="submit" name="Import" class="button defaultbutton"
               value="{'Import ontology'|i18n( 'easyontology/dashboard' )}"/>
    </div>
    <h2>{$class.name|wash()} - {$map.slug|wash()}</h2>
    {foreach $map.ontologies as $ontology}
        <p>{$ontology}</p>
    {/foreach}

    <div class="block float-break clearfix">
        <label for="concept">{'Concept'|i18n( 'easyontology/dashboard' )}</label>
        <input name="slug" id="concept" value="{cond(is_set($map.slug), $map.slug, '')}" />
    </div>

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
                    <label for="{$field}"
                           class="hidden hide">{'Map %field_name% to'|i18n( 'easyontology/dashboard',,hash('%field_name%', $field|wash()) )}</label>
                    {foreach $map.classes as $onto => $classes}
                        <div class="block">
                        <legend>{$onto}</legend>
                            {foreach $classes as $uri => $property}
                                <div class="element">
                                <input type="checkbox"
                                       data-uri="{$uri|wash()}"
                                       {foreach $property as $key => $value}data-{$key|explode(':')|implode('_')|wash()}="{$value|wash()}"{/foreach}
                                       value="{$uri|wash}"
                                       {if and(is_set($map.mapping[$field][$onto]), $map.mapping[$field][$onto]|contains($uri))}checked="checked"{/if}
                                       name="mapping[{$field}][{$onto}][]">
                                {if is_set($property['rdfs:label'])}{$property['rdfs:label']|wash()} ({$property['uri_basename']|wash()}){else}{$property['uri_basename']|wash()}{/if}
                                </div>
                            {/foreach}
                        </div>
                    {/foreach}
                </td>
            </tr>
        {/foreach}

        {foreach $class.data_map as $identifier => $field sequence array( bglight, bgdark ) as $sequence}
            <tr class="{$sequence}">
                <td>
                    <p>
                        {*<input type="checkbox" name="group[]" value="{$field.identifier}" />*}
                        <strong>{$field.name|wash()}</strong> - {$field.identifier}
                    </p>
                    <p>{$field.description|wash()}</p>
                    <p><em>{$field.data_type_string|wash()}</em></p>
                </td>
                <td>
                    <label for="{$field.identifier}"
                           class="hidden hide">{'Map %field_name% to'|i18n( 'easyontology/dashboard',,hash('%field_name%', $field.name|wash()) )}</label>
                    {foreach $map.grouped_properties as $onto => $properties}
                        <div class="block">
                            <legend>{$onto}</legend>
                            {foreach $properties as $uri => $property}
                                <div class="element">
                                    <input type="checkbox"
                                           {if and(is_set($map.mapping[$field.identifier][$onto]), $map.mapping[$field.identifier][$onto]|contains($uri))}checked="checked"{/if}
                                           name="mapping[{$field.identifier}][{$onto}][]"
                                           data-uri="{$uri|wash()}"
                                           {foreach $property as $key => $value}data-{$key|explode(':')|implode('_')|wash()}="{$value|wash()}"{/foreach}
                                           value="{$uri|wash}">
                                    {if is_set($property['rdfs:label'])}{$property['rdfs:label']|wash()} ({$property['uri_basename']|wash()}){else}{$property['uri_basename']|wash()}{/if}
                                </div>
                            {/foreach}
                        </div>
                    {/foreach}
                </td>
            </tr>
        {/foreach}

        {*
        {foreach $map.groups as $group_identifier => $fields sequence array( bglight, bgdark ) as $sequence}
            <tr class="{$sequence}">
                <td>
                    <p>
                        <a href="{concat('easyontology/remove/', $collection.classIdentifier, '/', $map.slug, '/', $group_identifier)|ezurl(no)}"><img src={"trash.png"|ezimage} alt="{'Remove'|i18n( 'easyontology/dashboard' )}" /></a>
                        <strong>{$group_identifier|wash()}</strong>
                    </p>
                    {foreach $fields as $field}
                        {if is_set($class.data_map[$field])}
                            <p><strong>{$class.data_map[$field].name|wash()}</strong> - {$class.data_map[$field].identifier}</p>
                        {/if}
                    {/foreach}
                </td>
                <td>
                    <label for="{$group_identifier}"
                           class="hidden hide">{'Map %field_name% to'|i18n( 'easyontology/dashboard',,hash('%field_name%', $group_identifier|wash()) )}</label>
                    {foreach $map.grouped_properties as $onto => $properties}
                        <div class="block">
                            <legend>{$onto}</legend>
                            {foreach $properties as $uri => $property}
                                <div class="element">
                                    <input type="checkbox"
                                           {if and(is_set($map.mapping[$group_identifier][$onto]), $map.mapping[$group_identifier][$onto]|contains($uri))}checked="checked"{/if}
                                           name="mapping[{$group_identifier}][{$onto}][]"
                                           data-uri="{$uri|wash()}"
                                            {foreach $property as $key => $value}data-{$key|explode(':')|implode('_')|wash()}="{$value|wash()}"{/foreach}
                                           value="{$uri|wash}">
                                    {if is_set($property['rdfs:label'])}{$property['rdfs:label']|wash()} ({$property['uri_basename']|wash()}){else}{$property['uri_basename']|wash()}{/if}
                                </div>
                            {/foreach}
                        </div>
                    {/foreach}
                </td>
            </tr>
        {/foreach}
        *}

        <tr>
            <td>
                {*<input type="text" name='group_identifier' value="{'New group identifier'|i18n( 'easyontology/dashboard' )}"/>
                <input type="submit" name='Group' class="button" value="{'Create field group'|i18n( 'easyontology/dashboard' )}"/>*}
            </td>
            <td style="text-align: right">
                <input type="submit" name='Store' class="button defaultbutton" value="{'Save'|i18n( 'easyontology/dashboard' )}"/>
            </td>
        </tr>
    </table>
</form>
{/if}
<script>{literal}
    $(document).ready(function () {
        var mapSelector = $('.map-selector');
        mapSelector.on('change', function () {
            var mapSelected = $(this).parent().find('.map-selected');
            mapSelected.empty();
            var data = $(this).find('option:selected').data();
            $.each(data, function (k, v) {
                $('<dt>' + k + '</dt>').appendTo(mapSelected);
                $('<dd>' + v + '</dd>').appendTo(mapSelected);
            });
        });
    });
    {/literal}</script>