<?php

declare(strict_types=1);

namespace Cissee\WebtreesExt\Modules;

use Cissee\WebtreesExt\Relationships\DefaultFullyMatchedPathJoiner;
use Cissee\WebtreesExt\Relationships\DefaultRelAlgorithm;
use Cissee\WebtreesExt\Relationships\RelDefBuilder;
use Cissee\WebtreesExt\Relationships\RelDefs;
use Cissee\WebtreesExt\Relationships\Times;
use Fisharebest\Webtrees\Module\AbstractModule;
use Illuminate\Support\Collection;

class LanguageGermanExt extends AbstractModule implements ModuleLanguageExtInterface {
  
  public function getRelationshipName(
          RelationshipPath $path): string {
    
    //modified splitting!
    $splitter = new class implements RelationshipPathSplitPredicate {
        public function prioritize(RelationshipPathSplit $split): int {
          
          //prefer splits resulting in common ancestor-based subpaths
          if (RelationshipPathSplitUtils::isNextToSpouse($split)) {
            return 3;
          }

          //then, splits without a sibling rel
          //(mainly for performance reasons)
          if (RelationshipPathSplitUtils::isAscentToDescent($split)) {
            return 2;
          }
          
          return 1;
        }
    };
            
    $algorithm = new DefaultRelAlgorithm(
            $splitter, 
            true); //minimize number of splits
    
    $joiner = new DefaultFullyMatchedPathJoiner();
    
    return $algorithm->getRelationshipName(
            self::defs(),
            $joiner,
            $path);
  }
  
  public static function defs(): RelDefs {
    
    $defs = [];
    
    $defs []= RelDefBuilder::def()->adoptiveFather()->is('Adoptivvater', 'des Adoptivvaters');
    $defs []= RelDefBuilder::def()->adoptiveMother()->is('Adoptivmutter', 'der Adoptivmutter');
    $defs []= RelDefBuilder::def()->adoptiveParent()->is('Adoptiv-Elternteil', 'des Adoptiv-Elternteils');
    
    $defs []= RelDefBuilder::def()->fosterFather()->is('Pflegevater', 'des Pflegevaters');
    $defs []= RelDefBuilder::def()->fosterMother()->is('Pflegemutter', 'der Pflegemutter');
    $defs []= RelDefBuilder::def()->fosterParent()->is('Pflege-Elternteil', 'des Pflege-Elternteils');
    
    $defs []= RelDefBuilder::def()->father()->is('Vater', 'des Vaters');
    $defs []= RelDefBuilder::def()->mother()->is('Mutter', 'der Mutter');
    $defs []= RelDefBuilder::def()->parent()->is('Elternteil', 'des Elternteils');

    ////////
    
    $defs []= RelDefBuilder::def()->husband()->is('Ehemann', 'des Ehemannes');
    $defs []= RelDefBuilder::def()->wife()->is('Ehefrau', 'der Ehefrau');
    $defs []= RelDefBuilder::def()->spouse()->is('Ehepartner', 'des Ehepartners');

    $defs []= RelDefBuilder::def()->exHusband()->is('Ex-Ehemann', 'des Ex-Ehemannes');
    $defs []= RelDefBuilder::def()->exWife()->is('Ex-Ehefrau', 'der Ex-Ehefrau');
    $defs []= RelDefBuilder::def()->exSpouse()->is('Ex-Ehepartner', 'des Ex-Ehepartners');

    $defs []= RelDefBuilder::def()->malePartner()->is('Partner', 'des Partners');
    $defs []= RelDefBuilder::def()->femalePartner()->is('Partnerin', 'der Partnerin');
    $defs []= RelDefBuilder::def()->partner()->is('Partner/Partnerin', 'des Partner/der Partnerin');

    ////////

    $defs []= RelDefBuilder::def()->adoptiveSon()->is('Adoptivsohn', 'des Adoptivsohnes');
    $defs []= RelDefBuilder::def()->adoptiveDaughter()->is('Adoptivtochter', 'der Adoptivtochter');
    $defs []= RelDefBuilder::def()->adoptiveChild()->is('Adoptivkind', 'des Adoptivkindes');
    
    $defs []= RelDefBuilder::def()->fosterSon()->is('Pflegesohn', 'des Pflegesohnes');
    $defs []= RelDefBuilder::def()->fosterDaughter()->is('Pflegetochter', 'der Pflegetochter');
    $defs []= RelDefBuilder::def()->fosterChild()->is('Pflegekind', 'des Pflegekindes');
    
    $defs []= RelDefBuilder::def()->son()->is('Sohn', 'des Sohnes');
    $defs []= RelDefBuilder::def()->daughter()->is('Tochter', 'der Tochter');
    $defs []= RelDefBuilder::def()->child()->is('Kind', 'des Kindes');
    
    ////////
    
    $defs []= RelDefBuilder::def()->twinBrother()->is('Zwillingsbruder', 'des Zwillingsbruders');
    $defs []= RelDefBuilder::def()->twinSister()->is('Zwillingsschwester', 'der Zwillingsschwester');
    $defs []= RelDefBuilder::def()->twinSibling()->is('Zwilling', 'des Zwillings');    

    $defs []= RelDefBuilder::def()->brother()->is('Bruder', 'des Bruders');
    $defs []= RelDefBuilder::def()->sister()->is('Schwester', 'der Schwester');
    $defs []= RelDefBuilder::def()->sibling()->is('Geschwisterteil', 'des Geschwisterteils');
    
    ////////

    //$ignoreLaterEvents: according to ?? 1590 BGB (https://www.gesetze-im-internet.de/bgb/__1590.html),
    //Schw??gerschaft is forever
    
    $defs []= RelDefBuilder::def()->spouse(true)->father()->is('Schwiegervater', 'des Schwiegervaters');
    $defs []= RelDefBuilder::def()->spouse(true)->mother()->is('Schwiegermutter', 'der Schwiegermutter');

    $defs []= RelDefBuilder::def()->child()->husband(true)->is('Schwiegersohn', 'des Schwiegersohns');
    $defs []= RelDefBuilder::def()->child()->wife(true)->is('Schwiegertochter', 'der Schwiegertochter');
    
    $defs []= RelDefBuilder::def()->spouse(true)->brother()->is('Schwager', 'des Schwagers');
    $defs []= RelDefBuilder::def()->sibling()->husband(true)->is('Schwager', 'des Schwagers');
    $defs []= RelDefBuilder::def()->spouse(true)->sister()->is('Schw??gerin', 'der Schw??gerin');
    $defs []= RelDefBuilder::def()->sibling()->wife(true)->is('Schw??gerin', 'der Schw??gerin');
        
    ////////

    $defs []= RelDefBuilder::def()->parent()->son()->is('Halbbruder', 'des Halbbruders');
    $defs []= RelDefBuilder::def()->parent()->daughter()->is('Halbschwester', 'der Halbschwester');
    
    $defs []= RelDefBuilder::def()->stepFather()->is('Stiefvater');
    $defs []= RelDefBuilder::def()->stepMother()->is('Stiefmutter');
    $defs []= RelDefBuilder::def()->stepParent()->is('Stief-Elternteil');
    
    $defs []= RelDefBuilder::def()->stepSon()->is('Stiefsohn');
    $defs []= RelDefBuilder::def()->stepDaughter()->is('Stieftochter');
    $defs []= RelDefBuilder::def()->stepChild()->is('Stiefkind');
    
    $defs []= RelDefBuilder::def()->stepBrother()->is('Stiefbruder');
    $defs []= RelDefBuilder::def()->stepSister()->is('Stiefschwester');
    $defs []= RelDefBuilder::def()->stepSibling()->is('Stief-Geschwisterteil');
    
    ////////
    
    $defs []= RelDefBuilder::def()->parent()->father()->is('Gro??vater', 'des Gro??vaters');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->father()->is('Urgro??vater', 'des Urgro??vaters');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->father()->is('Ururgro??vater', 'des Ururgro??vaters');
    $defs []= RelDefBuilder::def()->parent(Times::min(3))->parent()->father()->is('%s??Ur-Gro??vater', 'des %s??Ur-Gro??vaters');
    
    $defs []= RelDefBuilder::def()->parent()->mother()->is('Gro??mutter', 'der Gro??mutter');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->mother()->is('Urgro??mutter', 'der Urgro??mutter');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->mother()->is('Ururgro??mutter', 'der Ururgro??mutter');
    $defs []= RelDefBuilder::def()->parent(Times::min(3))->parent()->mother()->is('%s??Ur-Gro??mutter', 'der %s??Ur-Gro??mutter');

    $defs []= RelDefBuilder::def()->parent()->parent()->is('Gro??elternteil', 'des Gro??elternteils');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->parent()->is('Urgro??elternteil', 'des Urgro??elternteils');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->parent()->is('Ururgro??elternteil', 'des Ururgro??elternteils');
    $defs []= RelDefBuilder::def()->parent(Times::min(3))->parent()->parent()->is('%s??Ur-Gro??elternteil', 'des %s??Ur-Gro??elternteils');

    ////////

    $defs []= RelDefBuilder::def()->child()->son()->is('Enkelsohn', 'des Enkelsohns');
    $defs []= RelDefBuilder::def()->child(Times::fixed(2))->son()->is('Urenkelsohn', 'des Urenkelsohns');
    $defs []= RelDefBuilder::def()->child(Times::fixed(3))->son()->is('Ururenkelsohn', 'des Ururenkelsohns');
    $defs []= RelDefBuilder::def()->child(Times::min(3))->child()->son()->is('%s??Ur-Enkelsohn', 'des %s??Ur-Enkelsohns');
    
    $defs []= RelDefBuilder::def()->child()->daughter()->is('Enkeltochter', 'der Enkeltochter');
    $defs []= RelDefBuilder::def()->child(Times::fixed(2))->daughter()->is('Urenkeltochter', 'der Urenkeltochter');
    $defs []= RelDefBuilder::def()->child(Times::fixed(3))->daughter()->is('Ururenkeltochter', 'der Ururenkeltochter');
    $defs []= RelDefBuilder::def()->child(Times::min(3))->child()->daughter()->is('%s??Ur-Enkeltochter', 'der %s??Ur-Enkeltochter');
    
    $defs []= RelDefBuilder::def()->child()->child()->is('Enkelkind', 'der Enkelkindes');
    $defs []= RelDefBuilder::def()->child(Times::fixed(2))->child()->is('Urenkelkind', 'des Urenkelkindes');
    $defs []= RelDefBuilder::def()->child(Times::fixed(3))->child()->is('Ururenkelkind', 'des Ururenkelkindes');
    $defs []= RelDefBuilder::def()->child(Times::min(3))->child()->child()->is('%s??Ur-Enkelkind', 'des %s??Ur-Enkelkindes');

    ////////

    $defs []= RelDefBuilder::def()->parent()->brother()->is('Onkel', 'des Onkels');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->brother()->is('Gro??onkel', 'des Gro??onkels');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->brother()->is('Urgro??onkel', 'des Urgro??onkels');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(4))->brother()->is('Ururgro??onkel', 'des Ururgro??onkels');
    $defs []= RelDefBuilder::def()->parent(Times::min(5, -2))->brother()->is('%s??Ur-Gro??onkel', 'des %s??Ur-Gro??onkels');
    
    $defs []= RelDefBuilder::def()->parent()->sister()->is('Tante', 'der Tante');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->sister()->is('Gro??tante', 'der Gro??tante');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->sister()->is('Urgro??tante', 'der Urgro??tante');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(4))->sister()->is('Ururgro??tante', 'der Ururgro??tante');
    $defs []= RelDefBuilder::def()->parent(Times::min(5, -2))->sister()->is('%s??Ur-Gro??tante', 'der %s??Ur-Gro??tante');
        
    ////////

    $defs []= RelDefBuilder::def()->sibling()->son()->is('Neffe', 'des Neffen');
    $defs []= RelDefBuilder::def()->sibling()->child()->son()->is('Gro??neffe', 'des Gro??neffen');
    $defs []= RelDefBuilder::def()->sibling()->child(Times::fixed(2))->son()->is('Urgro??neffe', 'des Urgro??neffen');
    $defs []= RelDefBuilder::def()->sibling()->child(Times::fixed(3))->son()->is('Ururgro??neffe', 'des Ururgro??neffen');
    $defs []= RelDefBuilder::def()->sibling()->child(Times::min(4, -1))->son()->is('%s??Ur-Gro??neffe', 'des %s??Ur-Gro??neffen');

    $defs []= RelDefBuilder::def()->sibling()->daughter()->is('Nichte', 'der Nichte');
    $defs []= RelDefBuilder::def()->sibling()->child()->daughter()->is('Gro??nichte', 'der Gro??nichte');
    $defs []= RelDefBuilder::def()->sibling()->child(Times::fixed(2))->daughter()->is('Urgro??nichte', 'der Urgro??nichte');
    $defs []= RelDefBuilder::def()->sibling()->child(Times::fixed(3))->daughter()->is('Ururgro??nichte', 'der Ururgro??nichte');
    $defs []= RelDefBuilder::def()->sibling()->child(Times::min(4, -1))->daughter()->is('%s??Ur-Gro??nichte', 'der %s??Ur-Gro??nichte');
    
    ////////

    $defs []= RelDefBuilder::def()->parent()->sibling()->son()->is('Cousin', 'des Cousins');
    
    $ref = Times::min(1, 1); 
    $defs []= RelDefBuilder::def()->parent()->parent($ref)->sibling()->child($ref)->son()->is('Cousin %s. Grades', 'des Cousins %s. Grades');

    $defs []= RelDefBuilder::def()->parent()->sibling()->daughter()->is('Cousine', 'der Cousine');
    $defs []= RelDefBuilder::def()->parent()->parent($ref)->sibling()->child($ref)->daughter()->is('Cousine %s. Grades', 'der Cousine %s. Grades');
    
    ////////
    
    $ref = Times::min(1, 2);
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->sibling()->son()->is('Onkel 2. Grades', 'des Onkels 2. Grades');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->parent($ref)->sibling()->child($ref)->son()->is('Onkel %s. Grades', 'des Onkels %s. Grades');
    
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->sibling()->son()->is('Gro??onkel 2. Grades', 'des Gro??onkels 2. Grades');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->parent($ref)->sibling()->child($ref)->son()->is('Gro??onkel %s. Grades', 'des Gro??onkels %s. Grades');
    
    $defs []= RelDefBuilder::def()->parent(Times::fixed(4))->sibling()->son()->is('Urgro??onkel 2. Grades', 'des Urgro??onkels 2. Grades');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(4))->parent($ref)->sibling()->child($ref)->son()->is('Urgro??onkel %s. Grades', 'des Urgro??onkels %s. Grades');

    $defs []= RelDefBuilder::def()->parent(Times::fixed(5))->sibling()->son()->is('Ururgro??onkel 2. Grades', 'des Ururgro??onkels 2. Grades');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(5))->parent($ref)->sibling()->child($ref)->son()->is('Ururgro??onkel %s. Grades', 'des Ururgro??onkels %s. Grades');
    
    $defs []= RelDefBuilder::def()->parent(Times::min(6, -3))->sibling()->son()->is('%s??Ur-Gro??onkel 2. Grades', 'des %s??Ur-Gro??onkels 2. Grades');    
    $defs []= RelDefBuilder::def()->parent(Times::min(6, -3))->parent($ref)->sibling()->child($ref)->son()->is('%1$s??Ur-Gro??onkel %2$s. Grades', 'des %1$s??Ur-Gro??onkels %2$s. Grades');

    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->sibling()->daughter()->is('Tante 2. Grades', 'der Tante 2. Grades');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->parent($ref)->sibling()->child($ref)->daughter()->is('Tante %s. Grades', 'der Tante %s. Grades');
    
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->sibling()->daughter()->is('Gro??tante 2. Grades', 'der Gro??tante 2. Grades');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->parent($ref)->sibling()->child($ref)->daughter()->is('Gro??tante %s. Grades', 'der Gro??tante %s. Grades');
    
    $defs []= RelDefBuilder::def()->parent(Times::fixed(4))->sibling()->daughter()->is('Urgro??tante 2. Grades', 'der Urgro??tante 2. Grades');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(4))->parent($ref)->sibling()->child($ref)->daughter()->is('Urgro??tante %s. Grades', 'der Urgro??tante %s. Grades');

    $defs []= RelDefBuilder::def()->parent(Times::fixed(5))->sibling()->daughter()->is('Ururgro??tante 2. Grades', 'der Ururgro??tante 2. Grades');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(5))->parent($ref)->sibling()->child($ref)->daughter()->is('Ururgro??tante %s. Grades', 'der Ururgro??tante %s. Grades');
    
    $defs []= RelDefBuilder::def()->parent(Times::min(6, -3))->sibling()->daughter()->is('%s??Ur-Gro??tante 2. Grades', 'der %s??Ur-Gro??tante 2. Grades');    
    $defs []= RelDefBuilder::def()->parent(Times::min(6, -3))->parent($ref)->sibling()->child($ref)->daughter()->is('%1$s??Ur-Gro??tante %2$s. Grades', 'der %1$s??Ur-Gro??tante %2$s. Grades');
    
    ////////

    $ref = Times::min(1, 1);
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->son()->is('Neffe %s. Grades', 'des Neffen %s. Grades');
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->child()->son()->is('Gro??neffe %s. Grades', 'des Gro??neffen %s. Grades');
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->child(Times::fixed(2))->son()->is('Urgro??neffe %s. Grades', 'des Urgro??neffen %s. Grades');
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->child(Times::fixed(3))->son()->is('Ururgro??neffe %s. Grades', 'des Ururgro??neffen %s. Grades');
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->child(Times::min(4, -1))->son()->is('%2$s??Ur-Gro??neffe %1$s. Grades', 'des %2$s??Ur-Gro??neffen %1$s. Grades');
        
    $ref = Times::min(1, 1);
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->daughter()->is('Nichte %s. Grades', 'der Nichte %s. Grades');
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->child()->daughter()->is('Gro??nichte %s. Grades', 'der Gro??nichte %s. Grades');
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->child(Times::fixed(2))->daughter()->is('Urgro??nichte %s. Grades', 'der Urgro??nichte %s. Grades');
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->child(Times::fixed(3))->daughter()->is('Ururgro??nichte %s. Grades', 'der Ururgro??nichte %s. Grades');
    $defs []= RelDefBuilder::def()->parent($ref)->sibling()->child($ref)->child(Times::min(4, -1))->daughter()->is('%2$s??Ur-Gro??nichte %1$s. Grades', 'der %2$s??Ur-Gro??nichte %1$s. Grades');
    
    ////////
    
    return new RelDefs(new Collection($defs));
  }
}
