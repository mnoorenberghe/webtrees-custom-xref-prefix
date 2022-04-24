<?php

declare(strict_types=1);

namespace Cissee\WebtreesExt\Relationships;

use Cissee\WebtreesExt\Modules\RelationshipPath;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Illuminate\Support\Collection;

class StepSiblingRelationshipPathMatcher implements RelationshipPathMatcher {
  
  const CODES1 = array(
      'fat:fat' => 'M',
      'fat:par' => 'M',
      'mot:mot' => 'F',
      'mot:par' => 'F',
      'par:par' => 'U');
  
  const CODES2 = array(
      'hus:hus' => 'M',
      'hus:spo' => 'M',
      'wif:wif' => 'F',
      'wif:spo' => 'F',
      'spo:spo' => 'U');
  
  const CODES3 = array(    
      'son:son' => 'M',
      'son:chi' => 'M',
      'dau:dau' => 'F',
      'dau:chi' => 'F',
      'chi:chi' => 'U');
    
  protected $code1;
  protected $code2;
  protected $code3;

  public function minTimes(): int {
    return 3;
  }
  
  public function maxTimes(): int {
    return 3;
  }
  
  public function __construct(
          string $code1,
          string $code2,
          string $code3) {
    
    $this->code1 = $code1;
    $this->code2 = $code2;
    $this->code3 = $code3;
  }
  
  public function matchPath(
          int $matchedPathElements,
          bool $matchedPathDependsOnRemainingPath,
          RelationshipPath $path, 
          array $refs): Collection {    
    
    if ($path->isEmpty()) {
      return new Collection();
    }

    $split = $path->splitBefore(1);
    $head = $split->head();
    $tail = $split->tail();
      
    $sex = $this->match1($head->last()->rel());
    if ($sex === null) {
      return new Collection();
    }
    
    $child = $head->from();
    if ($child === null) {
      return new Collection();
    }
    
    $birthDate = $child->getBirthDate();
    if (!$birthDate->isOK()) {
      return new Collection();
    }
    
    ////////

    $split = $tail->splitBefore(1);
    $head = $split->head();
    $tail = $split->tail();
      
    $sex = $this->match2($head->last()->rel());
    if ($sex === null) {
      return new Collection();
    }
    
    $family = $head->last()->family();
    if ($family === null) {
      return new Collection();
    }
        
    $event = $family->facts(['ANUL', 'DIV', 'ENGA', 'MARR', '_NMR'], true, Auth::PRIV_HIDE, true)->last();
    
    if (!($event instanceof Fact)) {
      return new Collection();
    }
    
    if (!in_array($event->tag(), ['FAM:MARR'])) {
      return new Collection();
    }

    $eventDate = $event->date();
    if (!$eventDate->isOK()) {
      return new Collection();
    }
    
    //is the MARR after the BIRT?
    if ($birthDate->minimumJulianDay() >= $eventDate->maximumJulianDay()) {
      return new Collection();
    }
    
    ////////

    $split = $tail->splitBefore(1);
    $head = $split->head();
    $tail = $split->tail();
      
    $sex = $this->match3($head->last()->rel());
    if ($sex === null) {
      return new Collection();
    }
    
    $child = $head->last()->to();
    if ($child === null) {
      return new Collection();
    }
    
    $birthDate = $child->getBirthDate();
    if (!$birthDate->isOK()) {
      return new Collection();
    }
    
    //is the MARR after the (other) BIRT?
    if ($birthDate->minimumJulianDay() >= $eventDate->maximumJulianDay()) {
      return new Collection();
    }
    
    ////////
    
    //we have a match!
    //error_log("RelationshipPathMatcher matched fixed! ". $path . " as " . $sex);
    
    $ret = [];
    $ret []= new MatchedPartialPath($matchedPathElements + 3, $matchedPathDependsOnRemainingPath, $tail, $refs);
    return new Collection($ret);
  }
  
  public function match1(string $code): ?string {
    $key = $code . ":" . $this->code1;
    
    if (array_key_exists($key, self::CODES1)) {
      return self::CODES1[$key];
    }
    return null;
  }
  
  public function match2(string $code): ?string {
    $key = $code . ":" . $this->code2;
    
    if (array_key_exists($key, self::CODES2)) {
      return self::CODES2[$key];
    }
    return null;
  }
  
  public function match3(string $code): ?string {
    $key = $code . ":" . $this->code3;
    
    if (array_key_exists($key, self::CODES3)) {
      return self::CODES3[$key];
    }
    return null;
  }
}
