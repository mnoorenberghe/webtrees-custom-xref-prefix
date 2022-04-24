<?php

declare(strict_types=1);

namespace Cissee\WebtreesExt\Relationships;

use Cissee\WebtreesExt\Modules\RelationshipPath;
use Illuminate\Support\Collection;

//discouraged because partial matches are not cachable as they depend on the remaining path!
//better option is to use MatchedPathLengthRelationshipPathMatcher
class TotalPathLengthRelationshipPathMatcher implements RelationshipPathMatcher {

  protected $times;

  public function minTimes(): int {
    return 0; //we're only peeking, so we don't contribute here!
  }
  
  public function maxTimes(): int {
    return 0; //we're only peeking, so we don't contribute here!
  }
  
  public function __construct(
          Times $times) {
    
    $this->times = $times;
  }
  
  public function matchPath(
          int $matchedPathElements,
          bool $matchedPathDependsOnRemainingPath,
          RelationshipPath $path, 
          array $refs): Collection {    
    
    $totalPathLength = $matchedPathElements + $path->size();
    if ($this->times->minTimes() > $totalPathLength) {
      return new Collection();
    }
    
    $ret = [];    
    
    $nextRefs = [];
    foreach ($refs as $ref) {
      $nextRefs []= $ref;
    }
    $nextRefs []= new Reference($this->times, $totalPathLength);

    //dependsOnRemainingPath!
    $dependsOnRemainingPath = ($path->size() > 0);
    
    $ret []= new MatchedPartialPath(
            $matchedPathElements, 
            ($matchedPathDependsOnRemainingPath || $dependsOnRemainingPath), 
            $path, 
            $nextRefs);
      
    return new Collection($ret);
  }
}
