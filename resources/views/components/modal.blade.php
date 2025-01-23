<div class="modal fade mt-5" id="{{$id}}" tabindex="-1" role="dialog" aria-labelledby="{{$id}}Label" aria-hidden="true">
  <div class="modal-dialog mt-5" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="{{$id}}Label"> 
                @isset($heading)
                  {{ $heading }}
                @endisset
              </h5>
              <button type="button" id="close" class="close close-modal" data-dismiss="modal" aria-label="Close" data-target="#{{$id}}">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
            {{ $slot}}
          </div>
      </div>
  </div>
</div>