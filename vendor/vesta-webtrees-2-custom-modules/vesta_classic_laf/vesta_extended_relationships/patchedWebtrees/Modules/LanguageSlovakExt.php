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

class LanguageSlovakExt extends AbstractModule implements ModuleLanguageExtInterface {
  
  public function getRelationshipName(
          RelationshipPath $path): string {
    
    //modified splitting!
    $splitter = new class implements RelationshipPathSplitPredicate {
        public function prioritize(RelationshipPathSplit $split): int {
          
          //primarily prefer splits resulting in common ancestor-based subpaths
          if (RelationshipPathSplitUtils::isNextToSpouse($split)) {
            return 5;
          }
          
          //then, splits without a sibling rel
          //(mainly for performance reasons)
          if (RelationshipPathSplitUtils::isAscentToDescent($split)) {
            return 4;
          }
          
          //prefer 'sister' + 'great-grandson'
          //rather than 'niece' +'grandson'
          //(but use isNextToTerminalSibling instead of isNextToSibling in order to account for cases below)
          if (RelationshipPathSplitUtils::isNextToTerminalSibling($split)) {
            return 3;
          }
          
          //prefer 'grandfather' + 'second cousin'
          //rather than 'great-x3-grandfather' + 'great-x2-nephew'
          if (RelationshipPathSplitUtils::isWithinAscent($split)) {
            return 2;
          }
          
          //similar on the other side
          if (RelationshipPathSplitUtils::isWithinDescent($split)) {
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

    $defs []= RelDefBuilder::def()->father()->is('otec', 'otca');
    $defs []= RelDefBuilder::def()->mother()->is('matka', 'matky');
    $defs []= RelDefBuilder::def()->parent()->is('rodi??', 'rodi??a');

    $defs []= RelDefBuilder::def()->husband()->is('man??el', 'man??ela');
    $defs []= RelDefBuilder::def()->wife()->is('man??elka', 'man??elky');
    $defs []= RelDefBuilder::def()->spouse()->is('man??el/man??elka', 'man??ela/man??elky');

    $defs []= RelDefBuilder::def()->malePartner()->is('partner', 'partnera');
    $defs []= RelDefBuilder::def()->femalePartner()->is('partnerka', 'partnerky');

    $defs []= RelDefBuilder::def()->son()->is('syn', 'syna');
    $defs []= RelDefBuilder::def()->daughter()->is('dc??ra', 'dc??ry');
    $defs []= RelDefBuilder::def()->child()->is('die??a', 'de??a??a');

    $defs []= RelDefBuilder::def()->brother()->is('brat', 'brata');
    $defs []= RelDefBuilder::def()->sister()->is('sestra', 'sestry');
    $defs []= RelDefBuilder::def()->sibling()->is('s??rodenec', 's??rodenca');

    ////////

    $defs []= RelDefBuilder::def()->wife()->father()->is('tes??', 'tes??a');
    $defs []= RelDefBuilder::def()->wife()->mother()->is('testin??', 'testinej');
    $defs []= RelDefBuilder::def()->husband()->father()->is('svokor', 'svokra');
    $defs []= RelDefBuilder::def()->husband()->mother()->is('svokra', 'svokry');
    $defs []= RelDefBuilder::def()->spouse()->father()->is('svokor', 'svokra');
    $defs []= RelDefBuilder::def()->spouse()->mother()->is('svokra', 'svokry');

    $defs []= RelDefBuilder::def()->child()->husband()->is('za??', 'za??a');
    $defs []= RelDefBuilder::def()->child()->wife()->is('nevesta', 'nevesty');

    $defs []= RelDefBuilder::def()->spouse()->brother()->is('??vagor', '??vagra');
    $defs []= RelDefBuilder::def()->sibling()->husband()->is('??vagor', '??vagra');
    $defs []= RelDefBuilder::def()->spouse()->sister()->is('??vagrin??', '??vagrinej');
    $defs []= RelDefBuilder::def()->sibling()->wife()->is('??vagrin??', '??vagrinej');

    ////////

    $defs []= RelDefBuilder::def()->parent()->son()->is('nevlastn?? brat', 'nevlastn??ho brata');
    $defs []= RelDefBuilder::def()->parent()->daughter()->is('nevlastn?? sestra', 'nevlastnej sestry');

    //TODO: make step-x relationships available/configurable?

    ////////

    $defs []= RelDefBuilder::def()->parent()->father()->is('star?? otec', 'star??ho otca');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->father()->is('prastar?? otec', 'prastar??ho otca');
    $defs []= RelDefBuilder::def()->parent(Times::min(2))->parent()->father()->is('%s??prastar?? otec', '%s??prastar??ho otca');

    $defs []= RelDefBuilder::def()->parent()->mother()->is('star?? matka', 'starej matky');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->mother()->is('prastar?? matka', 'prastarej matky');
    $defs []= RelDefBuilder::def()->parent(Times::min(2))->parent()->mother()->is('%s??prastar?? matka', '%s??prastarej matky');

    $defs []= RelDefBuilder::def()->parent()->parent()->is('star?? rodi??', 'star??ho rodi??a');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->parent()->is('prastar?? rodi??', 'prastar??ho rodi??a');
    $defs []= RelDefBuilder::def()->parent(Times::min(2))->parent()->parent()->is('%s??prastar?? rodi??', '%s??prastar??ho rodi??a');

    ////////

    $defs []= RelDefBuilder::def()->child()->son()->is('vnuk', 'vnuka');
    $defs []= RelDefBuilder::def()->child(Times::fixed(2))->son()->is('pravnuk', 'pravnuka');
    $defs []= RelDefBuilder::def()->child(Times::min(2))->child()->son()->is('%s??pravnuk', '%s??pravnuka');

    $defs []= RelDefBuilder::def()->child()->daughter()->is('vnu??ka', 'vnu??ky');
    $defs []= RelDefBuilder::def()->child(Times::fixed(2))->daughter()->is('pravnu??ka', 'pravnu??ky');
    $defs []= RelDefBuilder::def()->child(Times::min(2))->child()->daughter()->is('%s??pravnu??ka', '%s??pravnu??ky');

    $defs []= RelDefBuilder::def()->child()->child()->is('vn????a', 'vn????a??a');
    $defs []= RelDefBuilder::def()->child(Times::fixed(2))->child()->is('pravn????a', 'pravn????a??a');
    $defs []= RelDefBuilder::def()->child(Times::min(2))->child()->child()->is('%s??pravn????a', '%s??pravn????a??a');

    ////////

    $defs []= RelDefBuilder::def()->father()->brother()->is('str??ko', 'str??ka');
    $defs []= RelDefBuilder::def()->father()->brother()->wife()->is('stryn??', 'strynej');
    $defs []= RelDefBuilder::def()->mother()->brother()->is('ujo', 'uja');
    $defs []= RelDefBuilder::def()->mother()->brother()->wife()->is('uj??in??', 'uj??inej');
    $defs []= RelDefBuilder::def()->parent()->brother()->is('str??ko', 'str??ka');

    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->brother()->is('prastr??ko', 'prastr??ka');
    // $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->brother()->is('pra-prastr??ko', 'pra-prastr??ka');
    // $defs []= RelDefBuilder::def()->parent(Times::min(4, -2))->brother()->is('%s?? prastr??ko', '%s?? prastr??ka');
    $defs []= RelDefBuilder::def()->parent()->sister()->is('teta', 'tety');
    $defs []= RelDefBuilder::def()->parent(Times::fixed(2))->sister()->is('prateta', 'pratety');
    // $defs []= RelDefBuilder::def()->parent(Times::fixed(3))->sister()->is('pra-prateta', 'pra-pratety');
    // $defs []= RelDefBuilder::def()->parent(Times::min(4, -2))->sister()->is('%s?? prateta', '%s?? pratety');

    ////////

    $defs []= RelDefBuilder::def()->sibling()->son()->is('synovec', 'synovca');
    $defs []= RelDefBuilder::def()->sibling()->child()->son()->is('prasynovec', 'prasynovca');
    // $defs []= RelDefBuilder::def()->sibling()->child(Times::min(2, -1))->son()->is('%s?? prasynovec', '%s?? prasynovca');
    $defs []= RelDefBuilder::def()->sibling()->daughter()->is('neter', 'netere');
    $defs []= RelDefBuilder::def()->sibling()->child()->daughter()->is('praneter', 'pranetere');
    // $defs []= RelDefBuilder::def()->sibling()->child(Times::min(2, -1))->daughter()->is('%s?? praneter', '%s?? pranetere');

    ////////

    $defs []= RelDefBuilder::def()->parent()->sibling()->son()->is('bratranec', 'bratranca');

    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(3))->sibling()->child(Times::fixed(3))->son()->is('bratranec zo 4. kolena', 'bratranca zo 4. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(5))->sibling()->child(Times::fixed(5))->son()->is('bratranec zo 6. kolena', 'bratranca zo 6. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(6))->sibling()->child(Times::fixed(6))->son()->is('bratranec zo 7. kolena', 'bratranca zo 7. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(13))->sibling()->child(Times::fixed(13))->son()->is('bratranec zo 14. kolena', 'bratranca zo 14. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(15))->sibling()->child(Times::fixed(15))->son()->is('bratranec zo 16. kolena', 'bratranca zo 16. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(16))->sibling()->child(Times::fixed(16))->son()->is('bratranec zo 17. kolena', 'bratranca zo 17. kolena');

    //IMPL NOTE: used as back-reference (i.e. count must match in '->child($ref)')
    $ref = Times::min(1, 1); 
    $defs []= RelDefBuilder::def()->parent()->parent($ref)->sibling()->child($ref)->son()->is('bratranec z %s. kolena', 'bratranca z %s. kolena');

    $defs []= RelDefBuilder::def()->parent()->sibling()->daughter()->is('sesternica', 'sesternice');

    //TODO WHY DONT THESE MATCH PARTIALLY????
    
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(3))->sibling()->child(Times::fixed(3))->daughter()->is('sesternica zo 4. kolena', 'sesternice zo 4. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(5))->sibling()->child(Times::fixed(5))->daughter()->is('sesternica zo 6. kolena', 'sesternice zo 6. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(6))->sibling()->child(Times::fixed(6))->daughter()->is('sesternica zo 7. kolena', 'sesternice zo 7. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(13))->sibling()->child(Times::fixed(13))->daughter()->is('sesternica zo 14. kolena', 'sesternice zo 14. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(15))->sibling()->child(Times::fixed(15))->daughter()->is('sesternica zo 16. kolena', 'sesternice zo 16. kolena');
    $defs []= RelDefBuilder::def()->parent()->parent(Times::fixed(16))->sibling()->child(Times::fixed(16))->daughter()->is('sesternica zo 17. kolena', 'sesternice zo 17. kolena');
 
    $ref = Times::min(1, 1);
    $defs []= RelDefBuilder::def()->parent()->parent($ref)->sibling()->child($ref)->daughter()->is('sesternica z %s. kolena', 'sesternice z %s. kolena');

    return new RelDefs(new Collection($defs));
  }
}