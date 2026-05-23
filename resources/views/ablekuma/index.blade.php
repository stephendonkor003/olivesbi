 @extends('layout.base')

 @section('page-styles')
 @endsection

 @section('page-content')
     <div class="container-fluid mh-auto">
         <div class="row">
             <div class="d-flex justify-content-between align-items-center mb-4">
                 <h4 class="heading mb-0">Ablekuma North Payments Management - 2024</h4>
                 <div class="d-flex align-items-center">
                     <ul class="nav nav-pills mix-chart-tab user-m-tabe" id="pills-tab" role="tablist">
                         <li class="nav-item" role="presentation">
                             <button class="nav-link" data-series="colm" id="pills-colm-tab" data-bs-toggle="pill"
                                 data-bs-target="#pills-colm" type="button" role="tab" aria-selected="false">
                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                     width="24px" height="24px" viewBox="0 0 24  24" version="1.1"
                                     class="svg-main-icon">
                                     <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                         <rect x="0" y="0" width="24" height="24" />
                                         <path
                                             d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z"
                                             fill="#000000" />
                                         <path
                                             d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z"
                                             fill="#000000" opacity="0.3" />
                                     </g>
                                 </svg>
                             </button>
                         </li>
                         <li class="nav-item" role="presentation">
                             <button class="nav-link active" data-series="list" id="pills-week-tab" data-bs-toggle="pill"
                                 data-bs-target="#pills-list" type="button" role="tab" aria-selected="true">

                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                     width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                     <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                         <rect x="0" y="0" width="24" height="24" />
                                         <rect fill="#000000" opacity="0.3" x="4" y="4" width="4" height="4"
                                             rx="1" />
                                         <path
                                             d="M5,10 L7,10 C7.55228475,10 8,10.4477153 8,11 L8,13 C8,13.5522847 7.55228475,14 7,14 L5,14 C4.44771525,14 4,13.5522847 4,13 L4,11 C4,10.4477153 4.44771525,10 5,10 Z M11,4 L13,4 C13.5522847,4 14,4.44771525 14,5 L14,7 C14,7.55228475 13.5522847,8 13,8 L11,8 C10.4477153,8 10,7.55228475 10,7 L10,5 C10,4.44771525 10.4477153,4 11,4 Z M11,10 L13,10 C13.5522847,10 14,10.4477153 14,11 L14,13 C14,13.5522847 13.5522847,14 13,14 L11,14 C10.4477153,14 10,13.5522847 10,13 L10,11 C10,10.4477153 10.4477153,10 11,10 Z M17,4 L19,4 C19.5522847,4 20,4.44771525 20,5 L20,7 C20,7.55228475 19.5522847,8 19,8 L17,8 C16.4477153,8 16,7.55228475 16,7 L16,5 C16,4.44771525 16.4477153,4 17,4 Z M17,10 L19,10 C19.5522847,10 20,10.4477153 20,11 L20,13 C20,13.5522847 19.5522847,14 19,14 L17,14 C16.4477153,14 16,13.5522847 16,13 L16,11 C16,10.4477153 16.4477153,10 17,10 Z M5,16 L7,16 C7.55228475,16 8,16.4477153 8,17 L8,19 C8,19.5522847 7.55228475,20 7,20 L5,20 C4.44771525,20 4,19.5522847 4,19 L4,17 C4,16.4477153 4.44771525,16 5,16 Z M11,16 L13,16 C13.5522847,16 14,16.4477153 14,17 L14,19 C14,19.5522847 13.5522847,20 13,20 L11,20 C10.4477153,20 10,19.5522847 10,19 L10,17 C10,16.4477153 10.4477153,16 11,16 Z M17,16 L19,16 C19.5522847,16 20,16.4477153 20,17 L20,19 C20,19.5522847 19.5522847,20 19,20 L17,20 C16.4477153,20 16,19.5522847 16,19 L16,17 C16,16.4477153 16.4477153,16 17,16 Z"
                                             fill="#000000" />
                                     </g>
                                 </svg>
                             </button>
                         </li>
                     </ul>
                     <button type="button" class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal"
                         data-bs-target="#exampleModalpopover">Take Payment </button>
                 </div>

             </div>


             <div class="col-xl-12 active-p">
                 <div class="tab-content" id="pills-tabContent">
                     <div class="tab-pane fade show active" id="pills-list" role="tabpanel"
                         aria-labelledby="pills-list-tab">

                         <div class="card">
                             <div class="card-body px-0">
                                 <div class="table-responsive active-projects user-tbl  dt-filter">
                                     <table id="user-tbl" class="table shorting">
                                         <thead>
                                             <tr>
                                                 <th>Action</th>
                                                 <th>Account</th>


                                             </tr>
                                         </thead>
                                         <tbody id="customers">

                                         </tbody>


                                     </table>
                                 </div>
                             </div>
                         </div>


                     </div>
                 </div>

             </div>
         </div>
     </div>
     </div>
 @endsection


 @section('page-scripts')
     <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->

     <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

     <script src="{{ asset('assets/vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
     <script src="{{ asset('assets/vendor/datatables/js/buttons.html5.min.js') }}"></script>
     <script src="{{ asset('assets/vendor/datatables/js/jszip.min.js') }}"></script>
     <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
 @endsection
