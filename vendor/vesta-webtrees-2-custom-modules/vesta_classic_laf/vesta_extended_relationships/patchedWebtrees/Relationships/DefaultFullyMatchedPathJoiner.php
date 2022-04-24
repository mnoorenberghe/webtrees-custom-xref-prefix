<?php

declare(strict_types=1);

namespace Cissee\WebtreesExt\Relationships;

use Cissee\WebtreesExt\MoreI18N;

class DefaultFullyMatchedPathJoiner implements FullyMatchedPathJoiner {
  
  public function join(FullyMatchedPath $a, FullyMatchedPath $b): FullyMatchedPath {
    $ag = $a->genitive();
    
    if ($ag === null) {
      //legacy join      
      //(note that even if we have genitive for b, we cannot use it transitively)
      return new FullyMatchedPath(
            //"Vater's Ehefrau"
            MoreI18N::xlate('%1$s’s %2$s', $a->nominative(), $b->nominative()), 
            null,
            $a->numberOfSplits() + $b->numberOfSplits());
    }
    $bg = $b->genitive();
    
    //$a "Vater", "des Vaters"
    //$b "Ehefrau", "der Ehefrau"
    return new FullyMatchedPath(
            $b->nominative() . ' ' . $ag, //"Ehefrau des Vaters"
            ($bg === null)?null:$bg . ' ' . $ag, //"der Ehefrau des Vaters"
            $a->numberOfSplits() + $b->numberOfSplits());
  }
}
