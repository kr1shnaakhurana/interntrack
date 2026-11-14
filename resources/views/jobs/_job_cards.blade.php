@foreach($jobs as $job)
    <div class="col-12 col-md-6 col-lg-4 job-item">
        <x-job-card :job="$job" />
    </div>
@endforeach

