
        <?php if ($count > 0) { 
            foreach ($list AS $row) { 
            $my_username = Auth::user()->username ;
            if ($my_username == $row->username) {
        ?>  
            <div class="d-flex justify-content-end mb-10"> 
                <div class="d-flex flex-column align-items-end"> 
                    <div class="d-flex align-items-center mb-2"> 
                        <div class="me-3">
                            <span class="text-muted fs-7 mb-1">{{ $row->comment_date }}</span>
                            <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary ms-1">{{ $row->fullname }}</a>
                        </div>  
                    </div> 
                    <div class="p-5 rounded bg-light-primary text-dark fw-bold mw-lg-400px text-end" data-kt-element="message-text">{{ $row->comment }}</div> 
                </div> 
            </div>

      <?php } else { ?>

        <div class="d-flex justify-content-start mb-10"> 
            <div class="d-flex flex-column align-items-start"> 
                <div class="d-flex align-items-center mb-2">  
                    <div class="ms-3">
                        <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary me-1">{{ $row->fullname }}</a>
                        <span class="text-muted fs-7 mb-1">{{ $row->comment_date }}</span>
                    </div> 
                </div> 
                <div class="p-5 rounded bg-light-info text-dark fw-bold mw-lg-400px text-start" data-kt-element="message-text">{{ $row->comment }}</div> 
            </div> 
        </div>
     

        <?php } } } else { ?>
            <div class="col-md-12 col-lg-12 col-xl-12"> 
                <div class="card h-100 flex-center bg-light-primary border-primary border border-dashed p-8"> 
                    <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/upload.svg class=" alt="" /> 
                    <a href="#" class="text-hover-primary fs-5 fw-bolder mb-2">(-_-)</a> 
                    <div class="fs-7 fw-bold text-gray-400">There are no conversations yet</div> 
                </div> 
            </div>
        <?php } ?>  