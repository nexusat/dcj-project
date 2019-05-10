<article class="card @isset($cssClass) {{ $cssClass }} @endisset">
  <a href="{{ route('conflict.show', $conflict) }}">
    <header class="card-divider grid-x align-justify">
        <h1 class="cell">
            <span class="year"> 
                {{ $conflict->year }}
            </span>

            {{ $conflict->location }}
        </h1>
    </header>

    <main class="card-section cell grid-x grid-margin-y">
        <div class="cell">
            {{ $conflict->side_a }}
                vs.
            {{ $conflict->side_b }}    
        </div>   

        @isset ( $conflict->territory )
            <div class="cell">
                Territory: {{ $conflict->territory }}
            </div>
        @endisset
    </main>

    <aside class="card-section grid-x">
        <section class="cell text-right">
            <p class="cell">
                UCDP Conflict ID: {{ $conflict->conflict_id }}
            </p>

            <p class="cell">
                Old Conflict ID: {{ $conflict->old_conflict_id }}
            </p>
        </section>
    </aside>
  </a>

    <footer class="card-divider grid-x">
        <p class="cell">
            Total During Justice Conflicts: {{ $conflict->justices_count }}
        </p>

        @can ('create', 'App\Justice')
            <a href="{{ route('justice.create', [
                    'conflict'=> $conflict->id
                ] ) }}" class="button small hollow">
                Add a new DCJ
            </a>
        @endcan
    </footer>
</article>
