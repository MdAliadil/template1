@extends('layouts.app')
@section('title', 'Payin Statement')
@section('pagetitle', 'Payin Statement')

@section('content')
   
        <div class="container-fluid p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand text-dark" href="#">API Documentation</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>
            <header class="bg-primary text-white text-center py-5 bg-dark">
                <h1 class="text-light">Welcome to the API Documentation</h1>
                <p class="lead">Everything you need to get started with our API</p>
            </header>
        </div>

        <div class="container-api-documentation">
            <!-- API Endpoint 1 -->

            <div class="card mb-3">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-10">
                            <p style="color:red;">
                                <strong>Endpoint:</strong> <code>GET /api/users/{id}</code>
                            </p>
                        </div>
                        <div class="col-sm-2">
                            <!-- DETAILS BUTTON -->
                            <button class="btn btn-info" data-bs-toggle="collapse" data-bs-target="#fullDetails">
                                Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Full Details Section -->
                <div id="fullDetails" class="collapse">
                    <div class="card-body">

                        <button class="copy-btn btn btn-sm btn-outline-secondary mb-2" onclick="copyText('combined-json')">
                            Copy
                        </button>
                        <pre class="bg-dark text-white p-3 rounded " id="combined-json" style="font-family: monospace;">

                        Request:
                        {
                            "id": #
                        }
                        
                        Response:
                        {
                            "id": {{ Auth::user()->id }},,
                            "name": {{ Auth::user()->name }},
                            "email": {{ Auth::user()->email }}
                        }
                            </pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Endpoint 2 -->
        <div class="card mb-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-10">
                        <p style="color:red;"><strong>Endpoint:</strong> <code>POST /api/users</code></p>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-success mb-2" data-bs-toggle="collapse" data-bs-target="#fulldetail">
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Full Details Section -->
            <div id="fulldetail" class="collapse">
                <div class="card-body">

                    <!-- Request & Response JSON -->
                    <h6>Request & Response Example:</h6>
                    <div class="position-relative mb-2">
                        <button class="copy-btn btn btn-sm btn-outline-secondary mb-2" onclick="copyText('combined-json')">
                            Copy
                        </button>
                        <pre class="bg-dark text-white p-3 rounded" id="combined-json" style="font-family: monospace;">

                        Request:
                        {
                            "id": #
                        }
                        
                        Response:
                        {
                            "id": {{ auth::user()->id }},,
                            "name": {{ auth::user()->name }},
                            "email": {{ auth::user()->email }}
                        }
                                </pre>
                    </div>
                </div>
            </div>
        </diV>
 
@endsection
@push('script')
<script>
    function copyText(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert("Copied to clipboard!");
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }
</script>
    
@endpush
