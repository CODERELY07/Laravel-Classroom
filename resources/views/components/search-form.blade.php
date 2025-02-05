@props(['id'])
<form id="userSearch" action="{{$action}}">
    @csrf
    <ul id="addedUsersList" class="list-unstyled mb-3">
    
    </ul>
    <input type="hidden" name="id" value="{{$id}}">
    <div class="d-flex">
        <input type="text" id="userSearchInput" class="form-control" name="email" placeholder="Search by email">
    </div>
    <div id="searchResults" class="mt-2">
        <!-- Live search results -->
    </div>
    <button type="submit" class="btn btn-primary float-end mt-5">Save Changes</button>
</form>