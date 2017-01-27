<?php

namespace StardogPhp\Sparql;

class UpdateBuilder
{

    const DEFAULT_PREFIXES = array(
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#'
    );

    private $prefixes;
    private $deletes;
    private $inserts;
    private $wheres;

    public static function create()
    {
        return new UpdateBuilder();
    }

    public function __construct()
    {
        $this->prefixes = static::DEFAULT_PREFIXES;
        $this->deletes = array();
        $this->inserts = array();
        $this->wheres = array();
    }

    public function addPrefix($prefix, $namespace)
    {
        $this->prefixes[ $prefix ] = $namespace;
        return $this;
    }

    public function addDelete($subject, $predicate, $value)
    {
        $this->deletes[] = array($subject, $predicate, $value);
        return $this;
    }

    public function addInsert($subject, $predicate, $value)
    {
        $this->inserts[] = array($subject, $predicate, $value);
        return $this;
    }

    public function addWhere($subject, $predicate, $value)
    {
        $this->wheres[] = array($subject, $predicate, $value);
        return $this;
    }

    public function buildSparqlUpdate()
    {
        $sparql = '';
        if ( count( $this->prefixes ) > 0 ) {
            foreach ( $this->prefixes as $prefix => $namespace ) {
                $namespace = $value = $this->normalizeUri( $namespace );
                $sparql .= "PREFIX $prefix: $namespace" . PHP_EOL;
            }
        }
        if ( count( $this->deletes ) > 0 ) {
            $sparql .= (count( $this->wheres ) > 0 ? 'DELETE {' : 'DELETE DATA {') . PHP_EOL;
            foreach ( $this->deletes as $triple ) {
                $subject = $this->normalizeSubject( $triple[ 0 ] );
                $predicate = $triple[ 1 ];
                $value = $this->normalizeValue( $triple[ 2 ] );
                $sparql .= "\t$subject $predicate $value ." . PHP_EOL;
            }
            $sparql .= '}' . PHP_EOL;
        }
        if ( count( $this->inserts ) > 0 ) {
            $sparql .= (count( $this->wheres ) > 0 ? 'INSERT {' : 'INSERT DATA {') . PHP_EOL;
            foreach ( $this->inserts as $triple ) {
                $subject = $this->normalizeSubject( $triple[ 0 ] );
                $predicate = $triple[ 1 ];
                $value = $this->normalizeValue( $triple[ 2 ] );
                $sparql .= "\t$subject $predicate $value ." . PHP_EOL;
            }
            $sparql .= '}' . PHP_EOL;
        }
        if ( count( $this->wheres ) > 0 ) {
            $sparql .= 'WHERE {' . PHP_EOL;
            foreach ( $this->wheres as $triple ) {
                $subject = $this->normalizeSubject( $triple[ 0 ] );
                $predicate = $triple[ 1 ];
                $value = $this->normalizeValue( $triple[ 2 ] );
                $sparql .= "\t$subject $predicate $value ." . PHP_EOL;
            }
            $sparql .= '}' . PHP_EOL;
        }
        return $sparql;
    }

    private function normalizeSubject($value)
    {
        if ( strpos( $value, 'http://' ) === false ) {
            return $value;
        } else {
            return $this->normalizeUri( $value );
        }
    }

    private function normalizeValue($value)
    {
        if ( strpos( $value, 'http://' ) !== false ) {
            return $this->normalizeUri( $value );
        } else if ( strpos( $value, '?' ) === 0 ) {
            return $value;
        } else {
            return '"' . $value . '"';
        }
    }

    private function normalizeUri($uri)
    {
        return "<" . $uri . ">";
    }

}