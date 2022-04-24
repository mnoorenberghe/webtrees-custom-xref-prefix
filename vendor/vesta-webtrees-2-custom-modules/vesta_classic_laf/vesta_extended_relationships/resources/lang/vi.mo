��    ]           �      �     �            )   7  "   a  "   �  \   �  �   	  r   �	  p   ;
  :   �
  B   �
  e   *  6  �     �     �     �     �  	   
  �        �  v   �     &    C     c  0   y  _   �     
  2   '  M   Z     �  "   �  $   �  F     )   V  w   �  q   �  .   j  4   �  )   �  8   �  W   1  y   �  �     �   �  �   +  n   �  d   S  V   �       B   +  �   n  ,        0  e   F  5   �  A   �     $  	   A  W   K     �     �     �  #   �  \   �  2   V  Q   �  {   �     W  2   m  #   �     �     �  4   �  �    _   �  u     0  }  �   �  �   i   q   �   �   h!  t   �!  -   f"  1   �"  V  �"  T   $  @   r$     �$  	   �$     �$     �$  �  �$     �&     �&  #   �&  %   '  !   8'     Z'  m   y'  �   �'  �   �(  �   �)  i   *  ^   �*  �   �*  �  f+     -     +-     D-     W-  
   o-  �   z-     9.  �   F.  3   �.  {   /     |0  B   �0  ~   �0  -   Y1  N   �1  {   �1  (   R2  F   {2  /   �2  ^   �2  R   Q3  �   �3  �   Z4  2   �4  H   +5  /   t5  @   �5  �   �5  �   f6  �   
7  �   �7  �   �8  �   m9  �   :  �   �:     6;  B   O;  �   �;  B   W<  "   �<  �   �<  `   A=  g   �=  6   
>  
   A>  {   L>     �>     �>     �>  $   ?  �   6?  L   �?  �   @  �   �@     9A  @   WA  =   �A     �A     �A  \   B  �  aB  �   .D  �   �D  �  kE  	  3G  �   =H  �   I  �   �I  �   �J  ?   BK  9   �K  �  �K  o   eM  ^   �M  	   4N     >N  *   RN     }N        =           M   X          L   F          7          E   &   '           W   U              J      /   1   (   C   P           %   ,       Z      ?   5       I      3   6             T          V                     B   2   $      +   !   	   9             -           4       ]       [   \          :                  
      S       K   G   @         Q   H                #   Y   R   D      .   )   N             <      A   ;           O           >   0   *   8   "         (see below for details). %1$s is %2$s of %3$s. (Number of relationships: %s) (that's overall almost as close as: %1$s) (that's overall as close as: %1$s) (that's overall closer than: %1$s) (that's overall not significantly closer than the closest relationship via common ancestors) A module providing various algorithms used to determine relationships. Includes a chart displaying relationships between two individuals, as a replacement for the original 'Relationships' module. A module providing various algorithms used to determine relationships. Includes an extended 'Relationships' chart. All paths between the two individuals that contribute to the CoR (Coefficient of Relationship), as defined here: Allow persistent toggle (user may show/hide relationships) And hopefully it shows how much better the new algorithm works ... Associated facts and events are displayed when the respective toggle checkbox is selected on the tab. Basically, each path (via common ancestors) between two individuals contributes to the CoR, inversely proportional to its length: Each path of length 2 (e.g. between full siblings) adds %1$s, each path of length 4 (e.g. between first cousins) adds %2$s, in general each path of length n adds (0.5)<sup>n</sup>. Calculating… Chart Settings Common ancestor:  Common ancestors:  Debugging Determines the shortest path between two individuals via a LCA (lowest common ancestor), i.e. a common ancestor who only appears on the path once. Display Displays additional relationship information via the extended 'Families' tab, and the extended 'Facts and Events' tab. Do not show any relationship Each SLCA (smallest lowest common ancestor) essentially represents a part of the tree which both individuals share (as part of their ancestors). More technically, the SLCA set of two individuals is a subset of the LCA set (excluding all LCAs that are themselves ancestors of other LCAs). Families Tab Settings Find a closest relationship via common ancestors Find a closest relationship via common ancestors, or fallback to the closest overall connection Find all overall connections Find all relationships via lowest common ancestors Find all smallest lowest common ancestors, show a closest connection for each Find other overall connections Find other/all overall connections Find the closest overall connections Find the closest overall connections (preferably via common ancestors) Finished - all link dates are up-to-date. For close relationships similar to the previous option, but faster. Internally just a combination of two other methods. For large trees, this process may initially take several minutes. You can always safely abort and continue later. How to determine relationships between parents How to determine relationships to associated persons How to determine relationships to spouses How to determine relationships to the default individual If this is unexpected, and there are recent changes, you may have to follow this link:  If this option is checked, relationships to the associated individuals are only shown for the following facts and events. If you do not want to change the functionality with respect to the original Facts and Events tab, select 'Do not show any relationship' in the first block. If you do not want to change the functionality with respect to the original Families tab, select 'Do not show any relationship' everywhere. If you do not want to use the chart functionality, hide this chart via Control Panel > Charts > %1$s Vesta Extended Relationships (note that the chart is listed under the module name). If you select this option everywhere, you should also disallow persistent toggle, as it has no visible effect. In particular if both lists are empty, relationships will never be shown for these facts and events. In that case, you should also disallow persistent toggle, as it has no visible effect. Individuals with Patriarchs Intended as a replacement for the original 'Relationships' module. It is more complicated to determine this exact CoR, and the differences are usually small anyway. Therefore, only the Uncorrected CoR is calculated. Legacy algorithm for Relationship path names No relationship found Note that the facts and events to be displayed at all may be filtered via the preferences of the tab. Only show relationships for specific facts and events Options referring to overall connections established before %1$s. Options to show in the chart Patriarch Prefers partial paths via common ancestors, even if there is no direct common ancestor. Relationship to me Relationship: %s Relationships Relationships between %1$s and %2$s Same option as in the original relationship chart, further configurable via recursion level: Same option as in the original relationship chart. Searching for all possible relationships can take a lot of time in complex trees. Searching for regular overall connections would be pointless here because there is always a trivial HUSB - WIFE connection. Show common ancestors Show common ancestors on top of relationship paths Show legacy relationship path names Swap individuals Synchronization Synchronize trees to obtain dated relationship links The CoR (Coefficient of Relationship) is proportional to the number of genes that two individuals have in common as a result of their genetic relationship. Its calculation is based on Sewall Wright's method of path coefficients. Its value represents the proportion of genes held in common by two related individuals over and above those held in common by the whole population. More details here:  The following options refer to the same algorithms as used in the extended relationships chart: The following options use dated links, i.e. links that have been established before the date of the respective event. The process should be repeated (but will finish much faster) whenever a tree is edited, otherwise you may obtain inconsistent relationship data (displayed relationships are always valid, but they may not actually have been established at the given date, if changes in the tree are not synchronized here). Therefore, if one of the following options is selected, overall connections are determined via dated links, i.e. links that have been established before the date of the respective event. These relationships may only be calculated efficiently by preprocessing the tree data, via the synchronization link at the top of this page. This allows you to present meaningful connections, such as 'widowed husband marries the sister of his dead wife'. This process calculates dates for all INDI - FAM links, so that relationships at a specific point in time can be calculated efficiently. This seems more useful in most cases (e.g. to determine the relationship to a godparent at the time of the baptism). Uncorrected CoR (Coefficient of Relationship) Uncorrected CoR (Coefficient of Relationship): %s Under normal circumstances the proportion of genes transmitted from ancestor to descendant &ndash; as estimated by Σ(0.5)<sup>n</sup> &ndash;  and the proportion of genes they hold in common (the true coefficient of relationship) are the same. If there has been any inbreeding, however, there may be a slight discrepancy, as explained here:  You can disable this via the module preferences, it's mainly intended for debugging. You may also adjust the access level of this part of the module. parents unlimited via legacy algorithm: %s view Project-Id-Version: Vietnamese (Vesta Webtrees Custom Modules)
Report-Msgid-Bugs-To: 
PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE
Last-Translator: FULL NAME <EMAIL@ADDRESS>
Language-Team: Vietnamese <https://hosted.weblate.org/projects/vesta-webtrees-custom-modules/vesta-extended-relationships/vi/>
Language: vi
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
X-Generator: Weblate 4.8-dev
  (Xem chi tiết bên dưới). %1$s là %2$s của %3$s. (Số lượng mối quan hệ: %s) (nhìn chung gần như bằng: %1$s) (tổng thể gần bằng: %1$s) (nhìn chung gần hơn: %1$s) (nhìn chung không gần hơn đáng kể so với mối quan hệ gần nhất thông qua tổ tiên chung) Một mô-đun cung cấp các thuật toán khác nhau được sử dụng để xác định các mối quan hệ. Bao gồm một biểu đồ hiển thị mối quan hệ giữa hai cá nhân, thay thế cho mô-đun 'Mối quan hệ' ban đầu. Một mô-đun cung cấp các thuật toán khác nhau được sử dụng để xác định các mối quan hệ. Bao gồm biểu đồ 'Mối quan hệ' mở rộng. Tất cả các con đường giữa hai cá nhân đóng góp vào CoR (Hệ số quan hệ), như được định nghĩa ở đây: Cho phép chuyển đổi liên tục (người dùng có thể hiển thị / ẩn các mối quan hệ) Và hy vọng nó cho thấy thuật toán mới hoạt động tốt hơn như thế nào ... Sự kiện và số liệu liên quan được hiển thị khi hộp kiểm chuyển đổi tương ứng được chọn trên tab. Về cơ bản, mỗi con đường (thông qua tổ tiên chung) giữa hai cá nhân đóng góp vào CoR, tỷ lệ nghịch với độ dài của nó: Mỗi con đường có độ dài 2 (ví dụ: giữa các anh chị em ruột) them %1$s, mỗi đường dẫn có độ dài 4 (ví dụ: giữa các anh chị em họ đầu tiên ) them %2$s, nói chung mỗi đường dẫn có độ dài n thêm (0,5) <sup> n </sup>. Đang tính toán… Cài đặt biểu đồ Tổ tiên chung:  Các Tổ tiên chung:  Gỡ lỗi Xác định đường đi ngắn nhất giữa hai cá thể thông qua LCA (tổ tiên chung thấp nhất), tức là tổ tiên chung chỉ xuất hiện trên đường đi một lần. Hiển thị Hiển thị thông tin mối quan hệ bổ sung qua tab 'Gia đình' mở rộng và tab 'Sự kiện và sự kiện' mở rộng. Không thể hiện bất kỳ mối quan hệ nào Mỗi SLCA (tổ tiên chung thấp nhất nhỏ nhất) về cơ bản đại diện cho một phần của cây mà cả hai cá thể đều chia sẻ (như một phần của tổ tiên của họ). Về mặt kỹ thuật, bộ SLCA của hai cá nhân là một tập hợp con của bộ LCA (không bao gồm tất cả các LCA tự là tổ tiên của các LCA khác). Cài đặt tab Gia đình Tìm mối quan hệ gần gũi nhất thông qua tổ tiên chung Tìm mối quan hệ gần nhất thông qua tổ tiên chung hoặc dự phòng cho mối quan hệ tổng thể gần nhất Tìm tất cả các kết nối tổng thể Tìm tất cả các mối quan hệ thông qua tổ tiên chung thấp nhất Tìm tất cả các tổ tiên chung gần nhất, thể hiện quan hệ họ hàng với từng người trong số họ Tìm các kết nối tổng thể khác Tìm các kết nối khác / tất cả các kết nối tổng thể Tìm các kết nối tổng thể gần nhất Tìm các kết nối tổng thể gần nhất (tốt nhất là thông qua tổ tiên chung) Đã hoàn tất - tất cả các ngày liên kết đều được cập nhật. Đối với các mối quan hệ thân thiết tương tự như tùy chọn trước đó, nhưng nhanh hơn. Bên trong chỉ là sự kết hợp của hai phương pháp khác. Đối với những cây lớn, quá trình này ban đầu có thể mất vài phút. Bạn luôn có thể hủy bỏ an toàn và tiếp tục sau đó. Cách xác định mối quan hệ giữa cha mẹ Cách xác định mối quan hệ với những người có liên quan Cách xác định mối quan hệ vợ chồng Cách xác định mối quan hệ với cá nhân mặc định Nếu điều này là không mong muốn và có những thay đổi gần đây, bạn có thể phải theo liên kết sau:  Nếu tùy chọn này được chọn, mối quan hệ với các cá nhân liên quan chỉ được hiển thị cho các sự kiện và số liệu sau đây. Nếu bạn không muốn thay đổi chức năng liên quan đến tab Sự kiện và Sự kiện ban đầu, hãy chọn 'Không hiển thị bất kỳ mối quan hệ nào' trong khối đầu tiên. Nếu bạn không muốn thay đổi chức năng liên quan đến tab Gia đình ban đầu, hãy chọn 'Không hiển thị bất kỳ mối quan hệ nào' ở mọi nơi. Nếu bạn không muốn sử dụng chức năng biểu đồ, hãy ẩn biểu đồ này qua Control Panel> Charts> %1$s Vesta Extended Relationships (lưu ý rằng biểu đồ được liệt kê dưới tên mô-đun). Nếu bạn chọn tùy chọn này ở mọi nơi, bạn cũng nên không cho phép chuyển đổi liên tục, vì nó không có tác dụng hiển thị. Đặc biệt nếu cả hai danh sách đều trống, các mối quan hệ sẽ không bao giờ được hiển thị cho các sự kiện và số liệu này. Trong trường hợp đó, bạn cũng không nên cho phép chuyển đổi liên tục, vì nó không có tác dụng rõ ràng. Cá nhân có Tổ phụ Dự định thay thế cho mô-đun 'Mối quan hệ' ban đầu. Việc xác định CoR chính xác này phức tạp hơn, và dù sao thì sự khác biệt thường rất nhỏ. Do đó, chỉ CoR không được điều chỉnh mới được tính toán. Thuật toán kế thừa cho tên đường dẫn mối quan hệ Không tìm thấy mối quan hệ Lưu ý rằng các dữ kiện và sự kiện sẽ được hiển thị có thể được lọc qua các tùy chọn của tab. Chỉ hiển thị các mối quan hệ đối với các sự kiện và số liệu cụ thể Các tùy chọn đề cập đến các kết nối tổng thể được thiết lập trước %1$s. Các tùy chọn để hiển thị trong biểu đồ Tổ phụ Ưu tiên con đường từng phần thông qua tổ tiên chung, ngay cả khi không có tổ tiên chung trực tiếp. Mối quan hệ với tôi Các mối quan hệ: %s Các mối quan hệ Mối quan hệ giữa %1$s và %2$s Tùy chọn tương tự như trong biểu đồ mối quan hệ ban đầu, có thể định cấu hình thêm thông qua mức đệ quy: Tùy chọn tương tự như trong biểu đồ mối quan hệ ban đầu. Tìm kiếm tất cả các mối quan hệ có thể có có thể mất rất nhiều thời gian trong các cây phức tạp. Việc tìm kiếm các kết nối tổng thể thông thường sẽ trở nên vô nghĩa ở đây vì luôn có một kết nối CHỒNG - VỢ bình thường. Hiển thị tổ tiên chung Hiển thị tổ tiên chung trên các con đường quan hệ Hiển thị tên đường dẫn mối quan hệ kế thừa Hoán đổi các cá nhân Đồng bộ hóa Đồng bộ hóa cây gia đình để có được các liên kết quan hệ ngày tháng CoR (Hệ số quan hệ) tỷ lệ thuận với số lượng gen mà hai cá thể có chung do mối quan hệ di truyền của họ. Tính toán của nó dựa trên phương pháp của Sewall Wright về hệ số đường đi. Giá trị của nó đại diện cho tỷ lệ các gen được giữ chung bởi hai cá thể có liên quan hơn và cao hơn những gen có điểm chung của cả quần thể. Thông tin chi tiết tại đây:  Các tùy chọn sau đề cập đến các thuật toán tương tự như được sử dụng trong biểu đồ mối quan hệ mở rộng: Các tùy chọn sau sử dụng các liên kết ngày tháng, tức là các liên kết đã được thiết lập trước ngày diễn ra sự kiện tương ứng. Quá trình nên được lặp lại (nhưng sẽ kết thúc nhanh hơn nhiều) bất cứ khi nào cây được chỉnh sửa, nếu không, bạn có thể nhận được dữ liệu về mối quan hệ không nhất quán (các mối quan hệ được hiển thị luôn hợp lệ, nhưng chúng có thể không thực sự được thiết lập vào ngày nhất định, nếu thay đổi trong cây không được đồng bộ ở đây). Do đó, nếu một trong các tùy chọn sau được chọn, các kết nối tổng thể được xác định thông qua các liên kết ngày tháng, tức là các liên kết đã được thiết lập trước ngày diễn ra sự kiện tương ứng. Các mối quan hệ này chỉ có thể được tính toán một cách hiệu quả bằng cách xử lý trước dữ liệu cây, thông qua liên kết đồng bộ hóa ở đầu trang này. Điều này cho phép bạn trình bày các kết nối có ý nghĩa, chẳng hạn như 'người chồng góa vợ kết hôn với em gái của người vợ đã chết'. Quá trình này tính toán ngày tháng cho tất cả các liên kết INDI - FAM, để các mối quan hệ tại một thời điểm cụ thể có thể được tính toán một cách hiệu quả. Điều này có vẻ hữu ích hơn trong hầu hết các trường hợp (ví dụ: để xác định mối quan hệ với cha mẹ đỡ đầu vào thời điểm rửa tội). CoR không được điều chỉnh (Hệ số mối quan hệ) CoR không hiệu chỉnh (Hệ số mối quan hệ): %s Trong những trường hợp bình thường, tỷ lệ gen được truyền từ tổ tiên sang con cháu &ndash; theo ước tính của Σ (0,5) <sup> n </sup> &ndash; và tỷ lệ các gen mà chúng giữ chung (hệ số quan hệ thực sự) là như nhau. Tuy nhiên, nếu đã có bất kỳ cuộc giao phối cận huyết nào, có thể có sự khác biệt nhỏ, như được giải thích ở đây:  Bạn có thể tắt tính năng này thông qua tùy chọn mô-đun, nó chủ yếu dùng để gỡ lỗi. Bạn cũng có thể điều chỉnh mức độ truy cập của phần này của mô-đun. bố mẹ không giới hạn thông qua thuật toán kế thừa: : %s xem 