  <!--start sidebar -->
        <aside class="sidebar-wrapper" data-simplebar="true">
          <div class="sidebar-header">
            <div>
              <img src="assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
            </div>
            <div>
              <h4 class="logo-text">Skodash</h4>
            </div>
            <div class="toggle-icon ms-auto"><i class="bi bi-chevron-double-left"></i>
            </div>
          </div>
          <!--navigation-->
          <ul class="metismenu" id="menu">
            <li>
              <a href="{{route('home')}}" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-house-door"></i>
                </div>
                <div class="menu-title">Dashboard</div>
              </a>
             
            </li>


            <li class="menu-label">UI Elements</li>
            <li>
              <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-award"></i>
                </div>
                <div class="menu-title">Report</div>
              </a>
              <ul>
                <li> <a href="{{route('statement', ['type' => 'upi'])}}"><i class="bi bi-arrow-right-short"></i>UPI Report</a>
                </li>
                <li> <a href="{{route('statement', ['type' => 'payout'])}}"><i class="bi bi-arrow-right-short"></i>Peyout Report</a>
                </li>
              </ul>
            </li>

            
            <li>
              <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-bag-check"></i>
                </div>
                <div class="menu-title">Payout Wallet</div>
              </a>
              <ul>
                <li> <a href="{{route('statement', ['type' => 'upiaccount'])}}"><i class="bi bi-arrow-right-short"></i>Products List</a>
                </li>
                <li> <a href="{{route('statement', ['type' => 'account'])}}"><i class="bi bi-arrow-right-short"></i>Payin Wallet</a>
               </li>
              </ul>
            </li>
          
            <li>
              <a href="{{route('apisetup', ['type' => 'document'])}}">
                <div class="parent-icon"><i class="bi bi-headset"></i>
                </div>
                <div class="menu-title">API Document</div>
              </a>
            </li>
           
          </ul>
          <!--end navigation-->
       </aside>

      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#menu').metisMenu();
    });

</script>