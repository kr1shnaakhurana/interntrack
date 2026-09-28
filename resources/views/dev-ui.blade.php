<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternTrack API Playground & Dev Console</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen font-inter">

    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col">
        <div class="p-4 border-b border-gray-700">
            <div class="text-indigo-400 text-sm font-medium uppercase tracking-wider mb-2">InternTrack API</div>
            <div class="text-gray-400 text-xs uppercase tracking-wider">Dev Console & API Tester</div>
        </div>

        <!-- User switcher -->
        <div class="p-4 bg-gray-700 border-b border-gray-700">
            <div class="text-gray-400 text-xs uppercase tracking-wider mb-2">Quick Login / Identity</div>
            <div class="grid grid-cols-3 gap-2 text-xs font-medium text-gray-300">
                <button class="btn-rounded bg-gray-600 px-2 py-1.5" onclick="quickLogin('admin@interntrack.com', 'admin123')">👑 Admin</button>
                <button class="btn-rounded bg-gray-600 px-2 py-1.5" onclick="quickLogin('aarav@university.edu', 'student123')">🎓 Student</button>
                <button class="btn-rounded bg-gray-600 px-2 py-1.5" onclick="quickLogin('hr@nexus.io', 'company123')">🏢 Company</button>
            </div>
            <div class="mt-2 text-gray-500 text-xs" id="active-user-label">Not Logged In</div>
            <div class="flex items-center gap-2 mt-2 text-xs text-gray-400">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 status-dot"></span>
                <span id="auth-status-dot" class="opacity-50">Not Logged In</span>
            </div>
        </div>

        <!-- Endpoints -->
        <div class="p-4 flex-1 overflow-y-auto">
            
            <div class="text-xs uppercase tracking-wider text-gray-400 mb-3">Authentication & User</div>
            <div class="space-y-1">
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/auth/login', samplePayloads.login)">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Auth Login
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/auth/register', samplePayloads.register)">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Register Student
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/auth/me', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> Current User Profile
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/auth/logout', null)">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Auth Logout
                </div>
            </div>

            <div class="text-xs uppercase tracking-wider text-indigo-400 mb-3 mt-4 border-b border-gray-700 pb-2">Job Discovery</div>
            <div class="space-y-1">
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/jobs?keyword=Engineering&is_remote=true', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> Search Jobs (Remote)
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/jobs', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> List All Jobs (Paginated)
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/company/jobs', samplePayloads.postJob)">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Company Post Job
                </div>
            </div>

            <div class="text-xs uppercase tracking-wider text-green-400 mb-3 mt-4 border-b border-gray-700 pb-2">Student Portal</div>
            <div class="space-y-1">
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/students/me', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> Student Profile
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/students/me/applications', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> My Applications
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/students/me/skills', samplePayloads.addSkill)">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Add Skill to Profile
                </div>
            </div>

            <div class="text-xs uppercase tracking-wider text-purple-400 mb-3 mt-4 border-b border-gray-700 pb-2">Applications & Interviews</div>
            <div class="space-y-1">
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/applications', samplePayloads.applyJob)">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Submit Application
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/applications', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> List Applications
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/applications/{app_id}/interviews', samplePayloads.scheduleInterview)">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Schedule Interview Round
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('PUT', '/api/v1/interviews/{int_id}', samplePayloads.updateInterview)">
                    <span class="method-badge method-PUT inline-block px-2 py-0.5 rounded text-xs font-medium">PUT</span> Update Round Result (Final)
                </div>
            </div>

            <div class="text-xs uppercase tracking-wolder text-orange-400 mb-3 mt-4 border-b border-gray-700 pb-2">Company Verification</div>
            <div class="space-y-1">
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/companies/{company_id}/verification', samplePayloads.requestVerification)">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Request Verification
                </div>
            </div>

            <div class="text-xs uppercase tracking-wider text-red-400 mb-3 mt-4 border-b border-gray-700 pb-2">Admin Console</div>
            <div class="space-y-1">
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/admin/dashboard', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> Admin Dashboard Metrics
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/admin/company-verifications', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> Pending Verifications
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('POST', '/api/v1/admin/ingest-external-jobs', {source: 'sample_external_feed'})">
                    <span class="method-badge method-POST inline-block px-2 py-0.5 rounded text-xs font-medium">POST</span> Run External Job Aggregator
                </div>
                <div class="px-3 py-2 rounded-md hover:bg-gray-700 cursor-pointer" onclick="loadEndpoint('GET', '/api/v1/admin/logs', null)">
                    <span class="method-badge method-GET inline-block px-2 py-0.5 rounded text-xs font-medium">GET</span> View Admin Audit Logs
                </div>
            </div>

        </div>
    </div>

    <!-- Workspace -->
    <div class="flex-1 flex flex-col bg-gray-900">

        
        <div class="p-4 border-b border-gray-700 bg-gray-800">
            <div class="flex items-center gap-3 max-w-6xl">
                <div class="endpoint-selector flex items-center gap-2 flex-1">
                    <select id="request-method" class="method-select rounded-md px-3 py-2 bg-gray-700 text-white font-medium">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                    <input type="text" id="request-url" class="url-input rounded-md flex-1 bg-gray-700 text-gray-100 px-3 py-2 font-mono text-sm" value="/api/v1/jobs">
                </div>
                <button class="btn-send rounded-md px-4 py-2 font-medium text-white" onclick="sendApiRequest()">
                    ▶ Run Request
                </button>
            </div>
        </div>

        <!-- Request / response -->
        <div class="p-4 flex-1 flex flex-col bg-gray-800 overflow-hidden">
            <div class="workspace-split grid grid-cols-1 lg:grid-cols-2 gap-1">

                <div class="pane">
                    <div class="pane-header px-4 py-3 border-b border-gray-700 text-xs uppercase tracking-wider font-medium text-gray-400">
                        <span>JSON REQUEST BODY</span>
                        <span class="text-gray-500 text-xs ml-2">(Optional for GET)</span>
                    </div>
                    <div class="editor-container rounded-md p-4 bg-gray-700 overflow-auto">
                        <textarea id="request-body" class="json-editor w-full bg-gray-800 text-gray-100 font-mono font-small py-3 leading-relaxed resize-none" placeholder="Enter JSON request payload..."></textarea>
                    </div>
                </div>

                <div class="pane">
                    <div class="pane-header px-4 py-3 border-b border-gray-700 text-xs uppercase tracking-wider font-medium text-gray-400">
                        <span>RESPONSE OUTPUT</span>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <span id="response-status" class="response-status d-none px-2 py-0.5 rounded text-xs">STATUS</span>
                            <span id="response-time" class="time-badge bg-gray-700 px-2 py-0.5 rounded text-xs"></span>
                        </div>
                    </div>
                    <pre id="response-body" class="response-viewer rounded-md p-4 bg-gray-800 font-mono font-small text-blue-400 overflow-auto whitespace-pre-wrap"></pre>
                </div>

            </div>
        </div>

    </div>

    <script>
        let bearerToken = localStorage.getItem('interntrack_token') || '';
        let currentUserEmail = localStorage.getItem('interntrack_email') || '';

        const samplePayloads = {
            login: {
                email: "admin@interntrack.com",
                password: "admin123"
            },
            register: {
                name: "New Candidate",
                email: "candidate" + Math.floor(Math.random()*1000) + "@student.edu",
                password: "student123",
                role: "student",
                program: "B.Tech Computer Science",
                department: "CSE",
                year: "4th Year"
            },
            postJob: {
                title: "Cloud & DevOps Intern",
                description: "Automate CI/CD pipelines and Kubernetes infrastructure.",
                job_type: "internship",
                location: "Remote / Bengaluru",
                is_remote: true,
                stipend_salary: "₹50,000 / month",
                deadline: "2026-12-31T23:59:59Z",
                skills_required: "Docker, Kubernetes, GitHub Actions, AWS"
            },
            applyJob: {
                job_id: "replace_with_actual_job_uuid",
                cover_letter_url: "https://storage.com/my_resume.pdf",
                expected_salary: "₹45,000 / month"
            },
            addSkill: {
                skill_name: "GraphQL",
                proficiency: "intermediate"
            },
            scheduleInterview: {
                round_name: "Technical Architecture",
                interview_type: "online",
                interviewer: "Lead DevOps Architect"
            },
            updateInterview: {
                result: "cleared",
                is_final_round: true,
                notes: "Candidate performed brilliantly in algorithm and system design."
            },
            requestVerification: {
                documents_url: "https://company.com/business_registration.pdf",
                remarks: "Official submission for verified corporate badge."
            }
        };

        function updateAuthUI() {
            const label = document.getElementById('active-user-label');
            const dot = document.getElementById('auth-status-dot');
            if (bearerToken) {
                label.innerText = currentUserEmail || 'Token Set';
                label.style.color = '#34d399';
                dot.classList.add('active');
                dot.classList.remove('opacity-50');
            } else {
                label.innerText = 'Not Logged In';
                label.style.color = '#78716c';
                dot.classList.remove('active');
                dot.classList.add('opacity-50');
            }
        }

        function quickLogin(email, password) {
            document.getElementById('request-method').value = 'POST';
            document.getElementById('request-url').value = '/api/v1/auth/login';
            document.getElementById('request-body').value = JSON.stringify({ email, password }, null, 2);
            sendApiRequest();
        }

        function loadEndpoint(method, url, payload) {
            document.getElementById('request-method').value = method;
            document.getElementById('request-url').value = url;
            if (payload) {
                document.getElementById('request-body').value = JSON.stringify(payload, null, 2);
            } else {
                document.getElementById('request-body').value = '';
            }
        }

        async function sendApiRequest() {
            const method = document.getElementById('request-method').value;
            let url = document.getElementById('request-url').value;
            const bodyText = document.getElementById('request-body').value.trim();
            const responseViewer = document.getElementById('response-body');
            const statusBadge = document.getElementById('response-status');
            const timeBadge = document.getElementById('response-time');

            responseViewer.innerText = "Executing request...";
            statusBadge.style.display = 'none';

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };

            if (bearerToken) {
                headers['Authorization'] = `Bearer ${bearerToken}`;
            }

            const options = { method, headers };

            if (['POST', 'PUT', 'PATCH'].includes(method) && bodyText) {
                try {
                    options.body = bodyText;
                } catch (e) {
                    alert("Invalid JSON in Request Body");
                    return;
                }
            }

            const startTime = performance.now();

            try {
                const res = await fetch(url, options);
                const duration = Math.round(performance.now() - startTime);
                const data = await res.json();

                statusBadge.innerText = `${res.status} ${res.statusText}`;
                statusBadge.className = `response-status status-${res.status} inline-block px-2 py-0.5 rounded text-xs`;
                statusBadge.style.display = 'inline-block';
                timeBadge.innerText = `${duration} ms`;
                timeBadge.className = `response-status status-${res.status} bg-gray-700 px-2 py-0.5 rounded text-xs inline-block`;

                responseViewer.innerText = JSON.stringify(data, null, 2);

                // Auto capture token on successful login/register
                if (data && data.data && data.data.token) {
                    bearerToken = data.data.token;
                    currentUserEmail = data.data.user ? data.data.user.email : '';
                    localStorage.setItem('interntrack_token', bearerToken);
                    localStorage.setItem('interntrack_email', currentUserEmail);
                    updateAuthUI();
                }
            } catch (err) {
                responseViewer.innerText = `Error: ${err.message}`;
            }
        }

        updateAuthUI();
        loadEndpoint('GET', '/api/v1/jobs', null);
    </script>
</body>
</html>