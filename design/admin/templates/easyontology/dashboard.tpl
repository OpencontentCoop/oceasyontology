{if count($collections)}
    <h2>{'Mapped classes'|i18n( 'easyontology/dashboard' )}</h2>
    <table class="table list table-striped">
        {foreach $collections as $collection}
            <tr>
                <td>{fetch(content, class, hash(class_id, $collection.classIdentifier)).name|wash()}</td>
                <td>
                    {foreach $collection.maps as $map}
                        <p>
                            <a href="{concat('easyontology/mapped/', $collection.classIdentifier, '/', $map.slug)|ezurl(no)}">{$map.slug|wash()}</a>

                            <a href="{concat('easyontology/mapper/', $collection.classIdentifier, '/', $map.slug)|ezurl(no)}"><img src={"edit.gif"|ezimage} alt="{'Edit'|i18n( 'easyontology/dashboard' )}" /></a>
                        </p>
                    {/foreach}
                </td>
            </tr>
        {/foreach}
    </table>
{/if}

<h2>{'Add class map'|i18n( 'easyontology/dashboard' )}</h2>
<form action="{'easyontology/dashboard'|ezurl(no)}" method="post" id="easyontology-add-collection-form">
    <div class="block float-break clearfix">
        <div class="element">
            {def $classlist = fetch( 'class', 'list', hash( 'sort_by', array( 'name', true() ) ) )}
            <label for="classIdentifier">{'Classes list'|i18n( 'easyontology/dashboard' )}</label>
            <select name="classIdentifier" id="classIdentifier">
                {foreach $classlist as $class}
                    {if $already_mapped|contains($class.identifier)|not()}
                        <option value="{$class.identifier|wash()}">{$class.name|wash()}</option>
                    {/if}
                {/foreach}
            </select>
            {undef $classlist}
        </div>
        <div class="element">
            <input type="submit" class="button" value="{'Add map'|i18n( 'easyontology/dashboard' )}"/>
        </div>
    </div>
</form>

<hr />

{if $ontologies|count()}
    <h2>{'Ontologies'|i18n( 'easyontology/dashboard' )}</h2>
    <table class="table list table-striped">
        {foreach $ontologies as $ontology}
            <tr>
                <td>
                    <a href="{concat('easyontology/inspect/', $ontology.slug)|ezurl(no)}">{$ontology.uri|wash()}</a>
                </td>
            </tr>
        {/foreach}
    </table>
{/if}

<h2>{'Add ontology from url'|i18n( 'easyontology/dashboard')}</h2>
<form action="{'easyontology/dashboard/'|ezurl(no)}" method="post">
    <div class="block float-break clearfix">
        <label for="source">{'Source url'|i18n( 'easyontology/dashboard' )}</label>
        <input type="text" name="source" id="source" class="box" value=""/>
        <label for="format">{'Format'|i18n( 'easyontology/dashboard' )}</label>
        <select name="format" id="format" class="span9">
            <option value="guess">Guess</option>
            <option value="php">RDF/PHP</option>
            <option value="json">RDF/JSON Resource-Centric</option>
            <option value="jsonld">JSON-LD</option>
            <option value="ntriples">N-Triples</option>
            <option value="turtle">Turtle Terse RDF Triple Language</option>
            <option selected="selected" value="rdfxml">RDF/XML</option>
            <option value="rdfa">RDFa</option>
        </select>
        <input type="submit" class="button defaultbutton"
               value="{'Import'|i18n( 'easyontology/dashboard' )}"/>
    </div>
</form>
