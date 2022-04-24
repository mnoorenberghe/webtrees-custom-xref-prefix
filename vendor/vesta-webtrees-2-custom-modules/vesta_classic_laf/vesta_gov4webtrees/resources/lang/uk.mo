��    V      �     |      x  M   y     �  �   �  3   �  z   �  l   /	  �   �	     0
  �   F
  2   �
  /     I   7  8   �  ^   �  ]     0  w  �   �  1   E     w     �  !   �  %   �  c   �  ?   <  6   |  ;   �  c   �  M   S  5   �  `   �  �   8  /   �  H     �   \  1   �       +   $  /   P  I   �  T   �  U     k   u     �  =   �  |   3  a   �          '  @   <     }  T   �     �               /  ?   D     �  3     :   8  �   s  y     x   �  �         �  [   �  "   (  ^   K  F   �  �   �  �   �     <  /   \  j   �  =   �     5     :     H  ]   g  ^   �     $     1     P  /   k     �  $   �    �  �   �!      j"    �"  r   �#  �   $  �   %  �   �%  )   �&    '  ^   (  f   o(  �   �(  l   p)  �   �)  �   u*    A+  �   S-  k   N.  9   �.     �.  E   /  L   Y/  	  �/  c   �0  W   1  v   l1  �   �1  �   �2  �   3  �   �3  H  w4  c   �5  �   $6    �6  Z   �7     (8  ^   D8  j   �8  �   9  |   �9  �   ':  �   �:     �;  w   �;    !<  �   )=     �=  7   �=     >  9   �>  �   �>  E   y?  ?   �?  ,   �?  8   ,@  X   e@  �   �@  ;   _A  g   �A  D  B  �   HC  �   0D  1  E  ?   :F  �   zF  U   QG  �   �G  d   �H  K  �H  �   4J  R   2K  _   �K  �   �K  ~   �L     M     'M  J   ;M  �   �M  �   1N     �N  >   �N  ?   O  B   NO     �O  D   �O         O   $   S   <       /                          5              >      -       !       E      @              *              J               B   R          	                  :   U            2      3          D             9                                  0       K   N       ;   T   .           &       %   V       8   C       G   1      F          7      ?          '   
   H   6                  I   4   +   L          "   =   A      (   )   Q   #       M   P   ,       'Classic' mode, extended to link to places from the GEDCOM data, if possible. 'Classic' mode. (Why is German in particular singled out like this? Because the GOV gazetteer is currently rather German-language centric, and therefore many places have German names). A module integrating GOV (historic gazetteer) data. A module integrating GOV (historic gazetteer) data. Enhances places with GOV data via the extended 'Facts and events' tab. According to the current GOV specification, settlements are not supposed to be parents of other settlements. Additionally, the module checks if the respective GOV id, or any of its parents within the hierarchy, has languages defined in the csv file '%1$s'. Administrative levels All data (except for the mapping of places to GOV ids, which has to be done manually) is retrieved from the GOV server and cached internally. Allow objects of type 'confederation' in hierarchy Allow objects of type 'settlement' in hierarchy As a final fallback, determine the place name according to this checkbox: Compact display (administrative levels only as tooltips) Consequently, place hierarchy information can only be changed indirectly, via the GOV website. Display a tooltip indicating the source of the GOV id. This is intended mainly for debugging. Execute requests to the GOV server via NuSOAP, rather than using the native php SoapClient. The native SoapClient is usually enabled (you can check this in your php.ini settings), but may not be provided by all hosters. If the native client is not enabled/available, this option is checked automatically. For a given place, this modules displays one or more names by matching the available names against a list of languages, according to the following strategy: For events with a date range, use the median date GOV Id Management GOV id GOV id for %1$s has been removed. GOV id for %1$s has been set to %2$s. GOV ids are stored outside GEDCOM data by default, but ids stored via _GOV tags are also supported. GOV place hierarchy for %1$s has been reloaded from GOV server. GOV place hierarchy has been reloaded from GOV server. If checked, attempt to fall back to the German place name.  If this is checked, the displayed GOV hierarchy uses place names from the GEDCOM data, if possible. If this option is checked, you usually want to disable the following option.  If unchecked, prefer any language other than German;  In particular, the Shared Places custom module may be used to manage GOV ids within GEDCOM data. In this case, the GOV ids are stored in a separate database table, which has to be managed manually when moving the respective tree to a different webtrees installation.  Internals (adjusted automatically if necessary) Invalid GOV id! Valid GOV ids are e.g. 'EITTZE_W3091', 'object_1086218'. It is recommended to use only one of the following options. You may also (temporarily) disable all editing via unchecking all of them. It will not be overwritten by subsequent updates. Local GOV data Look up a matching GOV id on the GOV server Mappings of places to GOV ids are not affected. Note: The mapping from place to GOV id is stored outside the gedcom data. Note: Ultimately it's probably preferable to correct the respective GOV data itself. Objects of this type arguably do not strictly belong to the administrative hierarchy. Otherwise, the start date is used (this is more consistent with other date-based calculations in webtrees). Outside GEDCOM data Outside GEDCOM data - editable by anyone (including visitors) Particularly useful in order to manage GOV ids via the Shared Places module. Ids are stored and exportable via GEDCOM tags.  Place hierarchies are displayed historically, i.e. according to the date of the respective event. Place names from GOV Place text and links Reset GOV id (outside GEDCOM) and reload the GOV place hierarchy Reset GOV id for %1$s Save the current id in order to reload the place hierarchy data from the GOV server. Set GOV id (outside GEDCOM) Set GOV id for %1$s Show GOV hierarchy for Show additional info Subsequently, all data is retrieved again from the GOV server.  The GOV server provides place names in different languages. However, there is no concept of an 'official language' for a place. The GOV server seems to be temporarily unavailable. The current user language always has the highest priority. These languages are then used, in the given order, either as fallbacks, or (if upper-cased) as additional languages (i.e. 'official languages' for a place hierarchy). This option mainly exists for demo servers and is not recommended otherwise. It has precedence over the preceding option. This policy hasn't been strictly followed though. Check this option if you end up with incomplete hierarchies otherwise. Uncheck this option if you do not want objects such as the European Union or the Holy Roman Empire to appear in hierarchies as parents of sovereign entities. Use NuSOAP instead of SoapClient Use place names and link to places existing in webtrees, additionally link to GOV via icons Use place names and links from GOV Use place names and links from GOV, additionally link to places existing in webtrees via icons Usually only required in case of substantial changes of the GOV data.  Vesta Gov4Webtrees: The displayed GOV hierarchy now additionally links to webtrees places where possible. You can switch back to the classic display (and others) via the module configuration. When this option is disabled, an alternative edit control is provided, which still allows to reload place hierarchies from the GOV server. Where to edit and store GOV ids Within GEDCOM data (via other custom modules).  You can create and modify this csv file according to your personal preferences, see '%1$s' for an example. You may also save an empty id in order to remove the mapping. both date of event fallback to German place names for events without a date, present time hierarchy will be used regardless of this preference. motivated by the assumption that place names in the local language are more useful in general  present time reload the GOV place hierarchy reset all cached data once this place does not exist at this point in time today unknown GOV type (newly introduced?) Project-Id-Version: Ukrainian (Vesta Webtrees Custom Modules)
Report-Msgid-Bugs-To: 
PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE
Last-Translator: FULL NAME <EMAIL@ADDRESS>
Language-Team: Ukrainian <https://hosted.weblate.org/projects/vesta-webtrees-custom-modules/vesta-gov4webtrees/uk/>
Language: uk
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;
X-Generator: Weblate 4.7-dev
 "Класичний" режим, по можливості розширений для посилання на місця з даних GEDCOM. "Класичний" режим. (Чому саме так виділяють німецьку мову? Оскільки географічний довідник GOV наразі є доволі німецькомовним, то в багатьох місцях є німецькі назви). Модуль, що інтегрує дані GOV (історичний географічний довідник). Модуль з інтеграції даних GOV (історичний географічний довідник). Покращує місця з даними GOV за допомогою розширеної вкладки "Факти та події". Відповідно до чинної специфікації GOV, населені пункти не повинні бути батьківськими по відношенню до інших населених пунктів. Крім того, модуль перевіряє, чи має відповідний ідентифікатор GOV (або будь-хто з його батьків у ієрархії) мови, визначені у файлі csv '%1$s'. Адміністративні рівні Усі дані (за винятком відображення місць до ідентифікаторів GOV, яке потрібно зробити вручну) отримуються з сервера GOV та кешуються всередині. Дозволити об’єкти типу „конфедерація” в ієрархії Дозволити об’єкти типу «поселення, колонія» в ієрархії Як остаточний резервний варіант визначте назву місця відповідно до цього прапорця: Компактний вигляд (адміністративні рівні лише як підказки) Отже, інформацію про ієрархію місць можна змінювати лише побічно через веб-сайт GOV. Відобразіть підказку із зазначенням джерела ідентифікатора GOV. Це призначено головним чином для налагодження. Виконувати запити до сервера GOV через NuSOAP, а не за допомогою власного php SoapClient. Власний SoapClient зазвичай увімкнено (ви можете перевірити це у налаштуваннях php.ini), але він може надаватися не всіма хостерами. Якщо власний клієнт не ввімкнено / не доступний, цей параметр відмічається автоматично. Для даного місця цей модуль відображає одне або кілька імен, зіставляючи наявні імена зі списком мов, відповідно до наступної стратегії: Для подій із діапазоном дат використовуйте серединну дату Управління ідентифікаторами GOV Ідентифікатор GOV Ідентифікатор GOV для %1$s було видалено. Ідентифікатор GOV для %1$s встановлено як %2$s. Ідентифікатори GOV за замовчуванням зберігаються поза даними GEDCOM, але також підтримуються ідентифікатори, що зберігаються за допомогою тегів _GOV. Ієрархію місця GOV для %1$s перезавантажено із сервера GOV. Ієрархію місця GOV перезавантажено із сервера GOV. Якщо відмічено, буде спроба повернутися до німецьких топонімів.  Якщо це встановлено, відображена ієрархія GOV використовує імена місць із даних GEDCOM, коли це можливо. Якщо цю опцію встановлено, зазвичай потрібно вимкнути наступну опцію.  Якщо не відмічено, перевагу буде віддано будь-якій іншій мові, крім німецької;  Зокрема, користувацький модуль Shared Places може використовуватися для управління ідентифікаторами GOV у даних GEDCOM. У цьому випадку ідентифікатори GOV зберігаються в окремій таблиці бази даних, якою потрібно керувати вручну під час переміщення відповідного дерева до іншої інсталяції веб-дерев.  Внутрішня будова (за потреби регулюються автоматично) Недійсний ідентифікатор GOV! Приклад дійсних ідентифікаторів GOV: 'EITTZE_W3091', 'object_1086218'. Рекомендується використовувати лише один із наведених нижче варіантів. Ви також можете (тимчасово) вимкнути всі редагування, знявши прапорці з усіх. Він не буде замінений під час наступних оновлень. Місцеві дані GOV Знайдіть відповідний ідентифікатор GOV на сервері GOV На відображення місць до ідентифікаторів GOV це не впливає. Зауваження: Зіставлення місця з ідентифікатором GOV зберігається за межами даних Gedcom. Зауваження: Зрештою, мабуть, краще відкоригувати відповідні дані GOV. Можливо, об’єкти цього типу не суворо належать до адміністративної ієрархії. В іншому випадку використовується дата початку (це більш узгоджується з іншими розрахунками на основі дати на webtrees). Поза даними GEDCOM Зовнішні дані GEDCOM - редагуються будь-ким (включаючи відвідувачів) Особливо корисно для управління ідентифікаторами GOV за допомогою модуля Shared Places. Ідентифікатори зберігаються та експортуються через теги GEDCOM.  Ієрархії місць відображаються історично, тобто відповідно до дати відповідної події. Топоніми від GOV Розмістіть текст та посилання Скинути ідентифікатор GOV (поза GEDCOM) і перезавантажте ієрархію місць GOV Скинути ідентифікатор GOV для %1$s Збережіть поточний ідентифікатор, щоб перезавантажити дані ієрархії місць із сервера GOV. Встановити ідентифікатор GOV (поза GEDCOM) Встановити ідентифікатор GOV для %1$s Показати ієрархію GOV для Показати додаткову інформацію Згодом усі дані знову отримуються із сервера GOV.  Сервер GOV надає назви місць різними мовами. Однак поняття "офіційна мова" місця не існує. Сервер GOV тимчасово недоступний. Поточна мова користувача завжди має найвищий пріоритет. Потім ці мови використовуються в заданому порядку або як резервні копії, або (якщо верхній регістр) використовуються як додаткові мови (тобто "офіційні мови" для ієрархії місця). Ця опція в основному існує для демонстраційних серверів, інакше не рекомендується. Він має перевагу над попереднім варіантом. Однак ця політика не суворо дотримується. Позначте цей параметр, якщо в іншому випадку ви отримаєте неповні ієрархії. Зніміть цей прапорець, якщо ви не хочете, щоб такі об'єкти, як Європейський Союз або Священна Римська імперія, відображалися в ієрархіях як батьки суверенних утворень. Використовувати NuSOAP замість SoapClient Використовуйте назви місць і посилання на місця, що існують на webtrees, додаткові посилання на GOV за допомогою піктограм Використовуйте назви місць та посилання від GOV Використовуйте назви місць та посилання від GOV, додатково посилання на місця, що існують на webtrees, за допомогою піктограм Зазвичай це потрібно лише у разі значних змін даних GOV.  Vesta Gov4Webtrees: Відображена ієрархія GOV тепер додатково посилається на місця webtrees, де це можливо. Ви можете повернутися до класичного вигляду (та інших) за допомогою конфігурації модуля. Коли цю опцію вимкнено, надається альтернативний контроль редагування, який все ще дозволяє перезавантажити ієрархії місць із сервера GOV. Де редагувати та зберігати ідентифікатори GOV В межах даних GEDCOM (через інші користувацькі модулі).  Ви можете створити та змінити цей файл csv відповідно до своїх особистих уподобань, див. приклад "%1$s". Ви також можете зберегти порожній ідентифікатор, щоб видалити мітку. обидва дата події запасний варіант до німецьких топонімів для подій без дати застосовуватиметься ієрархія поточного часу незалежно від цієї переваги. мотивовано припущенням, що топоніми місцевою мовою загалом корисніші  теперішній час перезавантажити ієрархію місця GOV скинути всі кешовані дані один раз на даний момент цього місця не існує сьогодні невідомий тип GOV (нещодавно введений?) 