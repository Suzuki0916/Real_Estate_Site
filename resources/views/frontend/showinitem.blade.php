<div class="row">
    {{ $show->links() }}
</div>
<div class="row">
    @forelse ($show as $item)
        <div class="col-12 mb-2">
            <div class="card mb-3 p-0">
                <div class="row g-0">
                    <div class="col-md-4">
                        <a href="{{ route('show_pro', $item->title_slug) }}">
                            <img class="img-fluid rounded-start w-100 h-100" style=""
                                src="{{ asset('/storage/property/' . $item->image) }}" alt="{{ $item->title }}">
                        </a>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <a class="btn p-0 text-secondary"
                                        href="{{ route('show_pro', $item->title_slug) }}">
                                        <h2 class="card-title">{{ $item->title }}</h2>
                                    </a>
                                </div>
                                <div class="col-12 mb-2">
                                    <p class="card-text mb-1">
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="{{ route('show_purpose', $item->purpose) }}">
                                            {{ ucfirst($item->purpose) }}
                                        </a>
                                        <a class="btn btn-sm btn-outline-secondary"
                                            href="{{ route('show_category', $item->Cate->slug_name) }}">
                                            {{ $item->Cate->name }}
                                        </a>
                                        <a class="btn btn-sm btn-outline-dark"
                                            href="{{ route('show_city', $item->City->slug_city) }}">
                                            {{ $item->City->city }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-12 mb-2">
                                    <p class="card-text">{{ $item->description }}</p>
                                </div>
                                <div class="col-12 mb-2">
                                    <p class="card-text">
                                        <i class="fas fa-bed"></i> {{ $item->rooms }}
                                        <i class="fas fa-shower"></i> {{ $item->bathrooms }}
                                    </p>
                                </div>
                                <div class="col-12 mt-4 mb-2">
                                    <a class="btn btn-primary" href="{{ route('show_pro', $item->title_slug) }}">
                                        <i class="fab fa-readme" aria-hidden="true"></i> Read more
                                    </a>
                                    @if ($status)
                                        <a class="btn
                                                @if (!empty($saved)) @if (in_array($item->title_slug, $saved)) btn-success
                                                @else
                                                btn-outline-success @endif
                                                @else
                                                btn-outline-success
                                                @endif
                                                save_pro"
                                            href="{{ route('save_pro_ajax', [$item->title_slug, $item->id]) }}">
                                            <i class="fa fa-bookmark" aria-hidden="true"></i>
                                            <span class="save_pro_text">
                                                @if (!empty($saved))
                                                    @if (in_array($item->title_slug, $saved))
                                                        Saved
                                                    @else
                                                        Save
                                                    @endif
                                                @else
                                                    Save
                                                @endif
                                            </span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 mb-2">
            <div class="card mb-3 p-0">
                <h2 class="text-center my-5">No Properties Fond</h2>
            </div>
        </div>
    @endforelse
</div>
