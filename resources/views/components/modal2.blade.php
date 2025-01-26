<div class="modal fade mt-5" id="{{$id}}" tabindex="-1" role="dialog" aria-labelledby="{{$id}}Label" aria-hidden="true">
    <div class="modal-dialog mt-5" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="close" class="close close-modal" data-dismiss="modal" aria-label="Close" data-target="#{{$id}}">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="{{$id . 'ID'}}">
                    @csrf
                    {{$slot}}
                    <div class="mt-4 float-end">
                        <button type="button" class="btn mx-3 close-modal" data-dismiss="modal" aria-label="Close" data-target="#{{$id}}">No</button>
                        <button type="submit" class="btn btn-danger close-modal" data-target="#{{$id}}">Yes</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
  </div>