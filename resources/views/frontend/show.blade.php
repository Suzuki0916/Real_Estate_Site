@extends('layouts.app')
@section('content_box')
    <div class="container">
        <div class="py-5">
            <div class="row">
                <div class="col-12 mb-3">
                    <h1>{{ $title }}</h1>
                </div>
            </div>
            <form id="filter" class="row" enctype="multipart/form-data">
                <div class="col-12 mb-3">
                    <div class="input-group">
                        <div class="input-group-text">Search</div>
                        <input class="form-control fltr" name="search" type="text" placeholder="Search by Name"
                            value="{{ $SecStr ?? '' }}">
                        <button class="btn btn-success" type="submit"><i class="fa fa-search" aria-hidden="true"></i>
                            Search</button>
                    </div>
                </div>
                <div class="col-3 mb-3">
                    <div class="input-group">
                        <div class="input-group-text">Category</div>
                        <select class="form-select fltr" name="category">
                            <option value="*">All</option>
                            @foreach ($cate as $item)
                                @if (!empty($cate_fltr))
                                    @if ($cate_fltr == $item->slug_name)
                                        <option selected value="{{ $item->slug_name }}">{{ $item->name }}</option>
                                    @else
                                        <option value="{{ $item->slug_name }}">{{ $item->name }}</option>
                                    @endif
                                @else
                                    <option value="{{ $item->slug_name }}">{{ $item->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="input-group">
                        <div class="input-group-text">City</div>
                        <select class="form-select fltr" name="city">
                            <option value="*">All</option>
                            @foreach ($city as $item)
                                @if (!empty($city_fltr))
                                    @if ($city_fltr == $item->slug_city)
                                        <option selected value="{{ $item->slug_city }}">{{ $item->city }}</option>
                                    @else
                                        <option value="{{ $item->slug_city }}">{{ $item->city }}</option>
                                    @endif
                                @else
                                    <option value="{{ $item->slug_city }}">{{ $item->city }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="input-group">
                        <div class="input-group-text">For</div>
                        <select class="form-select fltr" name="purpose">
                            @if (!empty($purpose_fltr))
                                <option {{ $purpose_fltr == '' ? 'selected' : '' }} value="*">All</option>
                                <option {{ $purpose_fltr == 'sale' ? 'selected' : '' }} value="sale">Sale</option>
                                <option {{ $purpose_fltr == 'rent' ? 'selected' : '' }} value="rent">Rent</option>
                                <option {{ $purpose_fltr == 'pg' ? 'selected' : '' }} value="pg">PG</option>
                            @else
                                <option value="*">All</option>
                                <option value="sale">Sale</option>
                                <option value="rent">Rent</option>
                                <option value="pg">PG</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-3 mb-3">
                    <div class="input-group">
                        <div class="input-group-text">Sort by</div>
                        <select class="form-select fltr" name="sort">
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                            <option value="phtl">Price High to Low</option>
                            <option value="plth">Price Low to High</option>
                            <option value="ahtl">Area High to Low</option>
                            <option value="alth">Area Low to High</option>
                        </select>
                    </div>
                </div>
                <div class="col-2 mb-2">
                    <button class="btn btn-primary w-100" type="submit"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </form>
            <div id="showbox">
                @include('frontend.showinitem')
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // console.log('Hello');
            $(document).on('submit', '#filter', function(e) {
                e.preventDefault();
                var formdata = $('#filter').serializeArray();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajaxFilter') }}",
                    data: formdata,
                    dataType: "HTML",
                    success: function(response) {
                        $('#showbox').html(response);
                    }
                });
                // console.log(formdata);
            });
            $(document).on('change keyup', '#filter .fltr', function(e) {
                e.preventDefault();
                var formdata = $('#filter').serializeArray();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajaxFilter') }}",
                    data: formdata,
                    dataType: "HTML",
                    success: function(response) {
                        $('#showbox').html(response);
                    }
                });
            });
            $(document).on('click', '#showbox .page-link', function(e) {
                e.preventDefault();
                var pagelink = $(this).attr('href');
                var formdata = $('#filter').serializeArray();

                $.ajax({
                    type: "GET",
                    url: pagelink,
                    data: formdata,
                    dataType: "HTML",
                    success: function(response) {
                        $('#showbox').html(response);
                    }
                });
            });
            $(document).on('click', '.save_pro', function(e) {
                e.preventDefault();
                var $this = $(this);
                var url = $(this).attr('href');
                var text = $(this).find('.save_pro_text').html().trim();

                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response) {
                        if (response) {
                            $this.find('.save_pro_text').html('Saved');
                            $this.addClass('btn-success').removeClass('btn-outline-success');
                        } else {
                            $this.find('.save_pro_text').html('Save');
                            $this.addClass('btn-outline-success').removeClass('btn-success');
                        }
                    }
                });
            });
        });
    </script>
@endsection
