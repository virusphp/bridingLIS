<div class="form-inline float-right">
    <form id="search" action="{{ $route }}" class="form-inline" role="search" >
        <div class="controls">
            <div class='input-group date {{ $errors->has('tgl') ? 'has-error' : '' }}' id='datetimepicker'>
                <input type='text' name="tgl" class="form-control" placeholder="Tanggal..." />
                <span class="input-group-addon">
                    <span class="fa fa-calendar">
                    </span>
                </span>
            </div>
        </div>
        <button type="submit" class="btn btn-secondary">
            <i class="fa fa-search"></i>
        </button>
    </form> 
    <form id="search"action="{{ $route }}" class="form-inline" role="search" >
        <div class="controls">
            <div class='input-group date {{ $errors->has('search') ? 'has-error' : '' }}'>
                <input type='text' name="search" class="form-control" placeholder="Cari..."/>
            </div>
        </div>
        <button type="submit" class="btn btn-secondary">
            <i class="fa fa-search"></i>
        </button>
    </form>
</div>