<?php

namespace Tests\StardohPhp\Stardog;

use StardogPhp\Sparql\QueryBuilder;
use StardogPhp\Stardog\Stardog;
use StardogPhp\Stardog\StardogBuilder;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{

    const SERVER = 'http://localhost';
    const PORT = '5820';
    const USER = 'admin';
    const PASSWORD = 'admin';
    const DB = 'myDB';

    /**
     * @var Stardog
     */
    private $stardog;

    protected function setUp()
    {
        $this->stardog = StardogBuilder::withServer( static::SERVER, static::PORT )
            ->setCredentials( static::USER, static::PASSWORD )
            ->build();
    }

    public function testAddTurtleContent()
    {
        $turtleContent = '@prefix foaf: <http://xmlns.com/foaf/0.1/> .
                    @prefix ns0: <http://scss.tcd.ie/cs/muc/linkedin/> .
                    @prefix ns1: <http://dbpedia.org/ontology/> .

                    <http://xmlns.com/foaf/0.1/Person/bbb50350e0b665c17ce6f3fd52d84312>
                      a <http://xmlns.com/foaf/0.1/Person/> ;
                      foaf:name "Roberto Paz Carracedo" ;
                      foaf:image <http://www.infojobs.net/ficha.foto?quina=716B4CD5-15C5-FEA7-E00C0B139A4C270A> ;
                      foaf:status "Analista programador ALTRAN", "I.T. Informatico con mas de 15 anos de experiencia como formador y programador web. Master Especialista en Programacion de Aplicaciones Web con Java .Net y PHP" ;
                      ns0:city ns0:Madrid ;
                      foaf:infojobsProfile <http://www.infojobs.net/roberto-paz-carracedo.prf> ;
                      ns0:skill ns0:arquitectura_de_la_informacion, ns0:composicion_y_autoedicion, ns0:diseno_con_javascript, ns0:diseno_de_plantillas_de_estilo, ns0:diseno_grafico_web, ns0:docencia_de_formacion_no_reglada, ns0:java, ns0:javabeans, ns0:java2e, ns0:joomla ;
                      ns1:language ns0:english .

                    <http://xmlns.com/foaf/0.1/Person/e7383a51828a53e4646cc75542ee23ed>
                      a <http://xmlns.com/foaf/0.1/Person/> ;
                      foaf:name "Juan Francisco Madrid Leiva" ;
                      foaf:image <http://www.infojobs.net/ficha.foto?quina=5491B24D-98A8-2D56-3DC0F0FA402945C7> ;
                      foaf:status "Docente de Experto en Cloud Computing Fopaem" ;
                      ns0:city ns0:Andalusia ;
                      foaf:infojobsProfile <http://www.infojobs.net/juanfrancisco-madrid-leiva.prf> ;
                      ns0:skill ns0:adobe_photoshop, ns0:android, ns0:css_cascading_style_sheets, ns0:html, ns0:java, ns0:php, ns0:programacion_de_aplicaciones_web, ns0:direccion_de_recursos_humanos ;
                      ns1:language ns0:spanish, ns0:english, ns0:french .

                    ';
        $this->stardog
            ->beginTransaction( static::DB )
            ->add( $turtleContent )
            ->commitTransaction();
    }

    public function testUpdate()
    {
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addWhere( "?s", "?p", "?v" )
            ->addDelete( "http://www.w3.org/People/Berners-Lee/", "?p", "?v" );
        $this->stardog->update( static::DB, $query );
    }

    public function testSelect()
    {
        $query = QueryBuilder::create()
            ->addSelect( array('?s', '?p', '?v') )
            ->addWhere( "?s", "?p", "?v" );
        $this->stardog->update( static::DB, $query );
    }

    public function testSelectWhereOptional()
    {
        $query = QueryBuilder::create()
            ->addSelect( array('?s', '?p', '?v') )
            ->addWhere( "?s", "?p", "?v" )
            ->addOptionalWhere( '?s', '?v', 'Name' );
        $this->stardog->update( static::DB, $query );
    }

}
