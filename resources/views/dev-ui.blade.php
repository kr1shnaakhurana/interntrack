<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternTrack API Playground & Dev Console</title>
    <!-- Modern Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090d16;
            --bg-card: #111827;
            --bg-sidebar: #0f172a;
            --border-color: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --accent-purple: #a855f7;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --code-bg: #030712;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 320px;
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .brand-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.15));
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .auth-bar {
            padding: 16px;
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid var(--border-color);
        }

        .auth-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .user-quick-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .btn-quick {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .btn-quick:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .token-status {
            margin-top: 10px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #020617;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--danger);
        }

        .status-dot.active {
            background: var(--success);
            box-shadow: 0 0 8px var(--success);
        }

        .nav-sections {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .nav-group-title {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 16px 8px 8px 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.82rem;
            color: #cbd5e1;
            cursor: pointer;
            transition: all 0.15s;
            margin-bottom: 2px;
        }

        .nav-item:hover, .nav-item.active {
            background-color: #1e293b;
            color: white;
        }

        .nav-item.active {
            border-left: 3px solid var(--primary);
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.15), transparent);
        }

        .method-badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Fira Code', monospace;
        }

        .method-GET { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .method-POST { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
        .method-PUT { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .method-DELETE { background: rgba(239, 68, 68, 0.15); color: #f87171; }

        /* Main Workspace */
        .main-workspace {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-dark);
            overflow: hidden;
        }

        .top-bar {
            padding: 16px 24px;
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .endpoint-selector {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            max-width: 800px;
        }

        .method-select {
            background: #020617;
            color: white;
            border: 1px solid var(--border-color);
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .url-input {
            flex: 1;
            background: #020617;
            color: #38bdf8;
            border: 1px solid var(--border-color);
            padding: 8px 14px;
            border-radius: 8px;
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
        }

        .btn-send {
            background: linear-gradient(135deg, var(--primary), var(--accent-purple));
            color: white;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-send:hover {
            opacity: 0.9;
        }

        .workspace-split {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1px;
            background-color: var(--border-color);
            overflow: hidden;
        }

        .pane {
            background-color: var(--bg-dark);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .pane-header {
            padding: 12px 20px;
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .editor-container {
            flex: 1;
            padding: 16px;
            overflow: auto;
        }

        textarea.json-editor {
            width: 100%;
            height: 100%;
            background: var(--code-bg);
            color: #e2e8f0;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 14px;
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            resize: none;
            outline: none;
            line-height: 1.5;
        }

        .response-viewer {
            flex: 1;
            background: var(--code-bg);
            margin: 16px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 16px;
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            overflow: auto;
            white-space: pre-wrap;
            color: #38bdf8;
        }

        .response-status {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .status-200, .status-201 { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .status-400, .status-401, .status-403, .status-422 { background: rgba(239, 68, 68, 0.2); color: #f87171; }

        .time-badge {
            color: var(--text-muted);
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand-header">
            <div class="brand-title">
                🚀 InternTrack API
            </div>
            <div class="brand-subtitle">Dev Console & API Tester</div>
        </div>

        <!-- Quick User Switcher -->
        <div class="auth-bar">
            <div class="auth-label">Quick Login / Identity</div>
            <div class="user-quick-buttons">
                <button class="btn-quick" onclick="quickLogin('admin@interntrack.com', 'admin123')">👑 Admin</button>
                <button class="btn-quick" onclick="quickLogin('aarav@university.edu', 'student123')">🎓 Student</button>
                <button class="btn-quick" onclick="quickLogin('hr@nexus.io', 'company123')">🏢 Company</button>
            </div>
            <div class="token-status">
                <span id="active-user-label" style="color: #94a3b8;">Not Logged In</span>
                <span class="status-dot" id="auth-status-dot"></span>
            </div>
        </div>

        <!-- API Endpoint List -->
        <div class="nav-sections">
            
            <div class="nav-group-title">Authentication & User</div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/auth/login', samplePayloads.login)">
                <span class="method-badge method-POST">POST</span> Auth Login
            </div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/auth/register', samplePayloads.register)">
                <span class="method-badge method-POST">POST</span> Register Student
            </div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/auth/me', null)">
                <span class="method-badge method-GET">GET</span> Current User Profile
            </div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/auth/logout', null)">
                <span class="method-badge method-POST">POST</span> Auth Logout
            </div>

            <div class="nav-group-title">Job Discovery</div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/jobs?keyword=Engineering&is_remote=true', null)">
                <span class="method-badge method-GET">GET</span> Search Jobs (Remote)
            </div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/jobs', null)">
                <span class="method-badge method-GET">GET</span> List All Jobs (Paginated)
            </div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/company/jobs', samplePayloads.postJob)">
                <span class="method-badge method-POST">POST</span> Company Post Job
            </div>

            <div class="nav-group-title">Student Portal</div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/students/me', null)">
                <span class="method-badge method-GET">GET</span> Student Profile
            </div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/students/me/applications', null)">
                <span class="method-badge method-GET">GET</span> My Applications
            </div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/students/me/skills', samplePayloads.addSkill)">
                <span class="method-badge method-POST">POST</span> Add Skill to Profile
            </div>

            <div class="nav-group-title">Applications & Interviews</div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/applications', samplePayloads.applyJob)">
                <span class="method-badge method-POST">POST</span> Submit Application
            </div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/applications', null)">
                <span class="method-badge method-GET">GET</span> List Applications
            </div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/applications/{app_id}/interviews', samplePayloads.scheduleInterview)">
                <span class="method-badge method-POST">POST</span> Schedule Interview Round
            </div>
            <div class="nav-item" onclick="loadEndpoint('PUT', '/api/v1/interviews/{int_id}', samplePayloads.updateInterview)">
                <span class="method-badge method-PUT">PUT</span> Update Round Result (Final)
            </div>

            <div class="nav-group-title">Company Verification</div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/companies/{company_id}/verification', samplePayloads.requestVerification)">
                <span class="method-badge method-POST">POST</span> Request Verification
            </div>

            <div class="nav-group-title">Admin Console</div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/admin/dashboard', null)">
                <span class="method-badge method-GET">GET</span> Admin Dashboard Metrics
            </div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/admin/company-verifications', null)">
                <span class="method-badge method-GET">GET</span> Pending Verifications
            </div>
            <div class="nav-item" onclick="loadEndpoint('POST', '/api/v1/admin/ingest-external-jobs', {source: 'sample_external_feed'})">
                <span class="method-badge method-POST">POST</span> Run External Job Aggregator
            </div>
            <div class="nav-item" onclick="loadEndpoint('GET', '/api/v1/admin/logs', null)">
                <span class="method-badge method-GET">GET</span> View Admin Audit Logs
            </div>

        </div>
    </div>

    <!-- Main Workspace -->
    <div class="main-workspace">
        
        <!-- Top Request Bar -->
        <div class="top-bar">
            <div class="endpoint-selector">
                <select id="request-method" class="method-select">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                </select>
                <input type="text" id="request-url" class="url-input" value="/api/v1/jobs">
            </div>
            <button class="btn-send" onclick="sendApiRequest()">
                ▶ Run Request
            </button>
        </div>

        <!-- Split Pane: Request Payload | Response Viewer -->
        <div class="workspace-split">
            <div class="pane">
                <div class="pane-header">
                    <span>JSON REQUEST BODY</span>
                    <span style="font-size: 0.7rem; color: #64748b;">(Optional for GET)</span>
                </div>
                <div class="editor-container">
                    <textarea id="request-body" class="json-editor" placeholder="Enter JSON request payload..."></textarea>
                </div>
            </div>

            <div class="pane">
                <div class="pane-header">
                    <span>RESPONSE OUTPUT</span>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span id="response-status" class="response-status status-200" style="display: none;">STATUS</span>
                        <span id="response-time" class="time-badge"></span>
                    </div>
                </div>
                <pre id="response-body" class="response-viewer">Click "Run Request" to execute an API endpoint.</pre>
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
            } else {
                label.innerText = 'Not Logged In';
                label.style.color = '#94a3b8';
                dot.classList.remove('active');
            }
        }

        async function quickLogin(email, password) {
            document.getElementById('request-method').value = 'POST';
            document.getElementById('request-url').value = '/api/v1/auth/login';
            document.getElementById('request-body').value = JSON.stringify({ email, password }, null, 2);
            await sendApiRequest();
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
                statusBadge.className = `response-status status-${res.status}`;
                statusBadge.style.display = 'inline-block';
                timeBadge.innerText = `${duration} ms`;

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
        // Load default endpoint
        loadEndpoint('GET', '/api/v1/jobs', null);
    </script>
</body>
</html>
