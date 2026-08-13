<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminLog;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\Company;
use App\Models\CompanyVerification;
use App\Models\Interview;
use App\Models\Job;
use App\Models\JobSkill;
use App\Models\JobSource;
use App\Models\Notification;
use App\Models\Resume;
use App\Models\SavedJob;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InternTrackSeeder extends Seeder
{
    public function run(): void
    {
        // ────────────────────────────────────────────────────────────────────
        // 1. ADMIN ACCOUNT
        //    Email: admin@interntrack.dev | Password: password
        // ────────────────────────────────────────────────────────────────────
        $adminUser = User::create([
            'name'      => 'Platform Admin',
            'email'     => 'admin@interntrack.dev',
            'password'  => 'password',
            'role'      => 'admin',
            'is_active' => true,
        ]);
        $admin = Admin::create([
            'user_id' => $adminUser->id,
            'name'    => 'Platform Admin',
        ]);

        // ────────────────────────────────────────────────────────────────────
        // 2. SKILLS LIBRARY
        // ────────────────────────────────────────────────────────────────────
        $skills = collect([
            ['name' => 'PHP',          'category' => 'Backend'],
            ['name' => 'Laravel',      'category' => 'Backend'],
            ['name' => 'Python',       'category' => 'AI/ML'],
            ['name' => 'Django',       'category' => 'Backend'],
            ['name' => 'React',        'category' => 'Frontend'],
            ['name' => 'Vue.js',       'category' => 'Frontend'],
            ['name' => 'Node.js',      'category' => 'Backend'],
            ['name' => 'TypeScript',   'category' => 'Frontend'],
            ['name' => 'PostgreSQL',   'category' => 'Database'],
            ['name' => 'MySQL',        'category' => 'Database'],
            ['name' => 'MongoDB',      'category' => 'Database'],
            ['name' => 'Docker',       'category' => 'DevOps'],
            ['name' => 'Kubernetes',   'category' => 'DevOps'],
            ['name' => 'AWS',          'category' => 'Cloud'],
            ['name' => 'Machine Learning', 'category' => 'AI/ML'],
            ['name' => 'Data Analysis', 'category' => 'Data'],
            ['name' => 'Excel',        'category' => 'Tools'],
            ['name' => 'Java',         'category' => 'Backend'],
            ['name' => 'Spring Boot',  'category' => 'Backend'],
            ['name' => 'Go',           'category' => 'Backend'],
            ['name' => 'Figma',        'category' => 'Design'],
            ['name' => 'UI/UX',        'category' => 'Design'],
        ])->map(fn($s) => Skill::create($s));

        $sk = $skills->keyBy('name');

        // ────────────────────────────────────────────────────────────────────
        // 3. JOB SOURCES
        // ────────────────────────────────────────────────────────────────────
        $directSource = JobSource::firstOrCreate(
            ['name' => 'InternTrack Direct'],
            ['type' => 'direct', 'is_active' => true]
        );
        $linkedinSource = JobSource::firstOrCreate(
            ['name' => 'LinkedIn Jobs'],
            ['type' => 'external', 'base_url' => 'https://linkedin.com/jobs', 'is_active' => true]
        );
        $internshalaSource = JobSource::firstOrCreate(
            ['name' => 'Internshala'],
            ['type' => 'external', 'base_url' => 'https://internshala.com', 'is_active' => true]
        );
        $govtSource = JobSource::firstOrCreate(
            ['name' => 'Govt Jobs Portal'],
            ['type' => 'external', 'base_url' => 'https://sarkari.gov.in', 'is_active' => true]
        );

        // ────────────────────────────────────────────────────────────────────
        // 4. COMPANIES
        // ────────────────────────────────────────────────────────────────────

        // 4a. VERIFIED COMPANY — primary test account
        //     Email: company@interntrack.dev | Password: password
        $companyUserA = User::create([
            'name'      => 'Nexus Innovations',
            'email'     => 'company@interntrack.dev',
            'password'  => 'password',
            'role'      => 'company',
            'is_active' => true,
        ]);
        $companyA = Company::create([
            'user_id'     => $companyUserA->id,
            'name'        => 'Nexus Innovations',
            'website'     => 'https://nexus.io',
            'industry'    => 'Software & AI',
            'size'        => '50-200 employees',
            'location'    => 'Bengaluru, India',
            'about'       => 'Building next-generation enterprise automation tools powered by AI.',
            'is_claimed'  => true,
            'sector'      => 'private',
            'is_external' => false,
        ]);
        CompanyVerification::create([
            'company_id'   => $companyA->id,
            'status'       => 'approved',
            'verified_by'  => $adminUser->id,
            'documents_url'=> 'https://nexus.io/docs/certificate.pdf',
            'remarks'      => 'Verified official corporate entity. Registration documents confirmed.',
            'applied_at'   => now()->subDays(14),
            'reviewed_at'  => now()->subDays(13),
            'is_latest'    => true,
        ]);

        // 4b. PENDING VERIFICATION COMPANY
        //     Email: hr@starlight.com | Password: password
        $companyUserB = User::create([
            'name'      => 'Starlight Digital',
            'email'     => 'hr@starlight.com',
            'password'  => 'password',
            'role'      => 'company',
            'is_active' => true,
        ]);
        $companyB = Company::create([
            'user_id'     => $companyUserB->id,
            'name'        => 'Starlight Digital',
            'website'     => 'https://starlight.com',
            'industry'    => 'Fintech',
            'size'        => '200-500 employees',
            'location'    => 'Mumbai, India',
            'about'       => 'Transforming digital payments, remittances and open banking.',
            'is_claimed'  => true,
            'sector'      => 'private',
            'is_external' => false,
        ]);
        CompanyVerification::create([
            'company_id'    => $companyB->id,
            'status'        => 'pending',
            'documents_url' => 'https://starlight.com/docs/gst.pdf',
            'remarks'       => 'Submitted GST certificate and incorporation documents for review.',
            'applied_at'    => now()->subDays(2),
            'is_latest'     => true,
        ]);

        // 4c. REJECTED COMPANY
        $companyUserC = User::create([
            'name'      => 'Phantom Ventures',
            'email'     => 'jobs@phantom.co',
            'password'  => 'password',
            'role'      => 'company',
            'is_active' => true,
        ]);
        $companyC = Company::create([
            'user_id'     => $companyUserC->id,
            'name'        => 'Phantom Ventures',
            'website'     => 'https://phantom.co',
            'industry'    => 'E-Commerce',
            'size'        => '10-50 employees',
            'location'    => 'Delhi, India',
            'is_claimed'  => true,
            'sector'      => 'private',
            'is_external' => false,
        ]);
        CompanyVerification::create([
            'company_id'    => $companyC->id,
            'status'        => 'rejected',
            'verified_by'   => $adminUser->id,
            'documents_url' => 'https://phantom.co/docs/fake.pdf',
            'remarks'       => 'Documents appear invalid or expired. Resubmission required with valid certificate.',
            'applied_at'    => now()->subDays(7),
            'reviewed_at'   => now()->subDays(6),
            'is_latest'     => true,
        ]);

        // 4d. UNVERIFIED COMPANY (no submission)
        $companyUserD = User::create([
            'name'      => 'GreenPath NGO',
            'email'     => 'info@greenpath.org',
            'password'  => 'password',
            'role'      => 'company',
            'is_active' => true,
        ]);
        $companyD = Company::create([
            'user_id'     => $companyUserD->id,
            'name'        => 'GreenPath NGO',
            'website'     => 'https://greenpath.org',
            'industry'    => 'Non-Profit / Environment',
            'size'        => '< 10 employees',
            'location'    => 'Pune, India',
            'is_claimed'  => true,
            'sector'      => 'ngo',
            'is_external' => false,
        ]);

        // 4e. External (aggregated) companies — no user account
        $extCompanyGoogle = Company::create([
            'name'        => 'Google India',
            'website'     => 'https://careers.google.com',
            'industry'    => 'Technology',
            'size'        => '10000+ employees',
            'location'    => 'Hyderabad, India',
            'sector'      => 'private',
            'is_external' => true,
            'is_claimed'  => false,
        ]);
        $extCompanyTCS = Company::create([
            'name'        => 'Tata Consultancy Services',
            'website'     => 'https://tcs.com/careers',
            'industry'    => 'IT Services',
            'size'        => '10000+ employees',
            'location'    => 'Pan India',
            'sector'      => 'private',
            'is_external' => true,
            'is_claimed'  => false,
        ]);
        $extCompanyISRO = Company::create([
            'name'        => 'ISRO – Indian Space Research Organisation',
            'website'     => 'https://isro.gov.in',
            'industry'    => 'Space & Defence Research',
            'size'        => '10000+ employees',
            'location'    => 'Bengaluru, India',
            'sector'      => 'government',
            'is_external' => true,
            'is_claimed'  => false,
        ]);

        // Add verified badges to external companies
        foreach ([$extCompanyGoogle, $extCompanyTCS] as $extCo) {
            CompanyVerification::create([
                'company_id'   => $extCo->id,
                'status'       => 'approved',
                'verified_by'  => $adminUser->id,
                'documents_url'=> $extCo->website,
                'remarks'      => 'Auto-verified well-known organisation.',
                'applied_at'   => now()->subDays(30),
                'reviewed_at'  => now()->subDays(29),
                'is_latest'    => true,
            ]);
        }

        // ────────────────────────────────────────────────────────────────────
        // 5. STUDENTS
        // ────────────────────────────────────────────────────────────────────

        // Primary test student
        // Email: student@interntrack.dev | Password: password
        $studentUserA = User::create([
            'name'      => 'Aarav Sharma',
            'email'     => 'student@interntrack.dev',
            'password'  => 'password',
            'role'      => 'student',
            'is_active' => true,
        ]);
        $studentA = Student::create([
            'user_id'    => $studentUserA->id,
            'roll_no'    => 'CS2026-089',
            'program'    => 'B.Tech Computer Science',
            'department' => 'CSE Department',
            'year'       => '4th',
            'phone'      => '+91-9876543210',
        ]);
        Resume::create([
            'student_id' => $studentA->id,
            'title'      => 'Software Intern Resume 2024',
            'file_url'   => 'https://drive.google.com/file/d/sample_resume_aarav',
            'version'    => 1,
            'is_default' => true,
        ]);
        Resume::create([
            'student_id' => $studentA->id,
            'title'      => 'Full Stack Developer Resume',
            'file_url'   => 'https://drive.google.com/file/d/sample_resume_aarav_v2',
            'version'    => 2,
            'is_default' => false,
        ]);
        foreach ([$sk['PHP'], $sk['Laravel'], $sk['PostgreSQL'], $sk['React'], $sk['Docker']] as $i => $skill) {
            StudentSkill::create([
                'id'          => (string) Str::uuid(),
                'student_id'  => $studentA->id,
                'skill_id'    => $skill->id,
                'proficiency' => $i < 2 ? 'advanced' : 'intermediate',
            ]);
        }

        // Second student
        $studentUserB = User::create([
            'name'      => 'Priya Patel',
            'email'     => 'priya@university.edu',
            'password'  => 'password',
            'role'      => 'student',
            'is_active' => true,
        ]);
        $studentB = Student::create([
            'user_id'    => $studentUserB->id,
            'roll_no'    => 'AI2026-014',
            'program'    => 'M.Tech Artificial Intelligence',
            'department' => 'AI Lab',
            'year'       => '2nd',
            'phone'      => '+91-9876543211',
        ]);
        Resume::create([
            'student_id' => $studentB->id,
            'title'      => 'AI/ML Research Resume',
            'file_url'   => 'https://drive.google.com/file/d/sample_resume_priya',
            'version'    => 1,
            'is_default' => true,
        ]);
        foreach ([$sk['Python'], $sk['Machine Learning'], $sk['Data Analysis'], $sk['React']] as $skill) {
            StudentSkill::create([
                'id'         => (string) Str::uuid(),
                'student_id' => $studentB->id,
                'skill_id'   => $skill->id,
                'proficiency'=> 'advanced',
            ]);
        }

        // Third student
        $studentUserC = User::create([
            'name'      => 'Rahul Mehta',
            'email'     => 'rahul@college.ac.in',
            'password'  => 'password',
            'role'      => 'student',
            'is_active' => true,
        ]);
        $studentC = Student::create([
            'user_id'    => $studentUserC->id,
            'roll_no'    => 'MCA2025-033',
            'program'    => 'MCA – Master of Computer Applications',
            'department' => 'Computer Science',
            'year'       => '3rd',
            'phone'      => '+91-9876543212',
        ]);
        Resume::create([
            'student_id' => $studentC->id,
            'title'      => 'Java Backend Developer Resume',
            'file_url'   => 'https://drive.google.com/file/d/sample_resume_rahul',
            'version'    => 1,
            'is_default' => true,
        ]);
        foreach ([$sk['Java'], $sk['Spring Boot'], $sk['MySQL'], $sk['Docker']] as $skill) {
            StudentSkill::create([
                'id'         => (string) Str::uuid(),
                'student_id' => $studentC->id,
                'skill_id'   => $skill->id,
                'proficiency'=> 'intermediate',
            ]);
        }

        // ────────────────────────────────────────────────────────────────────
        // 6. DIRECT JOBS (from verified InternTrack companies)
        // ────────────────────────────────────────────────────────────────────

        $job1 = Job::create([
            'company_id'     => $companyA->id,
            'source_id'      => $directSource->id,
            'title'          => 'Full-Stack Engineering Intern',
            'description'    => "Join Nexus Innovations as a Full-Stack Engineering Intern and work on our enterprise automation platform.\n\n**Responsibilities:**\n- Build and maintain Laravel-based REST APIs\n- Develop React.js frontends with TypeScript\n- Write PostgreSQL queries and migrations\n- Participate in code reviews and Agile sprints\n\n**Requirements:**\n- Strong foundation in PHP/Laravel or equivalent backend framework\n- Familiarity with React or Vue.js\n- Understanding of RESTful API design\n- Good communication skills\n\n**Perks:**\n- ₹45,000/month stipend\n- Remote-friendly environment\n- Direct mentorship from senior engineers\n- Pre-placement offer based on performance",
            'job_type'       => 'internship',
            'location'       => 'Bengaluru, India',
            'is_remote'      => true,
            'stipend_salary' => '₹45,000 / month',
            'deadline'       => now()->addDays(30),
            'skills_required'=> 'PHP, Laravel, React, TypeScript, PostgreSQL',
            'is_active'      => true,
            'posted_at'      => now()->subDays(3),
        ]);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job1->id, 'skill_id' => $sk['Laravel']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job1->id, 'skill_id' => $sk['React']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job1->id, 'skill_id' => $sk['PostgreSQL']->id, 'importance' => 'medium']);

        $job2 = Job::create([
            'company_id'     => $companyA->id,
            'source_id'      => $directSource->id,
            'title'          => 'AI Research Fellowship',
            'description'    => "A 6-month fully-funded AI research fellowship at Nexus Innovations working on LLM-powered automation systems.\n\n**Focus Areas:**\n- Large Language Model fine-tuning\n- Autonomous agent development with tool use\n- Evaluation pipelines for AI reliability\n\n**Eligibility:**\n- M.Tech / PhD students in CS, AI, or Mathematics\n- Strong Python and ML framework knowledge\n- Published or submitted research papers preferred\n\n**Compensation:**\n- ₹60,000/month stipend\n- Full research budget access\n- Co-authorship on publications",
            'job_type'       => 'fellowship',
            'location'       => 'Bengaluru, India',
            'is_remote'      => true,
            'stipend_salary' => '₹60,000 / month',
            'deadline'       => now()->addDays(45),
            'skills_required'=> 'Python, Machine Learning, PyTorch, Research Writing',
            'is_active'      => true,
            'posted_at'      => now()->subDays(1),
        ]);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job2->id, 'skill_id' => $sk['Python']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job2->id, 'skill_id' => $sk['Machine Learning']->id, 'importance' => 'high']);

        $job3 = Job::create([
            'company_id'     => $companyA->id,
            'source_id'      => $directSource->id,
            'title'          => 'DevOps Engineer (Full-Time)',
            'description'    => "Scale our infrastructure across 3 cloud regions handling millions of API requests daily.\n\n**Tech Stack:**\n- Kubernetes (GKE) + Helm\n- Terraform for IaC\n- Prometheus/Grafana monitoring\n- CI/CD: GitHub Actions + ArgoCD\n\n**Experience Required:**\n- 1-3 years DevOps/SRE experience\n- Strong Docker and Kubernetes knowledge\n- Experience with production incident management",
            'job_type'       => 'full-time',
            'location'       => 'Bengaluru, India',
            'is_remote'      => false,
            'stipend_salary' => '₹12–18 LPA',
            'deadline'       => now()->addDays(21),
            'skills_required'=> 'Docker, Kubernetes, AWS, Terraform, CI/CD',
            'is_active'      => true,
            'posted_at'      => now()->subDays(5),
        ]);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job3->id, 'skill_id' => $sk['Docker']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job3->id, 'skill_id' => $sk['Kubernetes']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job3->id, 'skill_id' => $sk['AWS']->id, 'importance' => 'medium']);

        $job4 = Job::create([
            'company_id'     => $companyB->id,
            'source_id'      => $directSource->id,
            'title'          => 'Fintech Backend Engineer',
            'description'    => "Build secure, high-throughput REST API services powering payment gateway integrations across 12 countries.\n\n**You will:**\n- Design and implement payment processing microservices\n- Integrate with NPCI, Visa, and Swift payment networks\n- Implement multi-level encryption and fraud detection hooks\n- Conduct performance testing under load\n\n**Requirements:**\n- PHP or Node.js backend experience\n- Understanding of PCI-DSS compliance\n- PostgreSQL or similar RDBMS\n- API security best practices",
            'job_type'       => 'full-time',
            'location'       => 'Mumbai, India',
            'is_remote'      => false,
            'stipend_salary' => '₹14–20 LPA',
            'deadline'       => now()->addDays(25),
            'skills_required'=> 'PHP, Node.js, PostgreSQL, Docker, API Security',
            'is_active'      => true,
            'posted_at'      => now()->subDays(2),
        ]);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job4->id, 'skill_id' => $sk['PHP']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $job4->id, 'skill_id' => $sk['PostgreSQL']->id, 'importance' => 'high']);

        // ────────────────────────────────────────────────────────────────────
        // 7. EXTERNAL / AGGREGATED JOBS
        // ────────────────────────────────────────────────────────────────────

        $extJob1 = Job::create([
            'company_id'     => $extCompanyGoogle->id,
            'source_id'      => $linkedinSource->id,
            'source_job_id'  => 'goog-swe-intern-2024-blr',
            'title'          => 'Software Engineering Intern – Google STEP',
            'description'    => "Google's STEP Internship Program is a 12-week paid internship for first and second-year undergraduate students with a passion for software engineering.\n\n**What you'll do:**\n- Code, test and launch features used by billions of users\n- Work with a Google mentor and buddy\n- Present your project at the end of internship\n\n**Minimum Qualifications:**\n- Currently enrolled in a Bachelor's programme in CS or related\n- Programming experience in Java, Python, C++ or Go\n- Returning to school after the internship",
            'job_type'       => 'internship',
            'location'       => 'Hyderabad, India',
            'is_remote'      => false,
            'stipend_salary' => '₹1,20,000 / month',
            'deadline'       => now()->addDays(60),
            'skills_required'=> 'Java, Python, Go, Algorithms, Data Structures',
            'job_url'        => 'https://careers.google.com/jobs/results/step-intern-2024',
            'is_active'      => true,
            'posted_at'      => now()->subDays(7),
        ]);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob1->id, 'skill_id' => $sk['Python']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob1->id, 'skill_id' => $sk['Java']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob1->id, 'skill_id' => $sk['Go']->id, 'importance' => 'medium']);

        $extJob2 = Job::create([
            'company_id'     => $extCompanyTCS->id,
            'source_id'      => $internshalaSource->id,
            'source_job_id'  => 'tcs-smart-hiring-2024',
            'title'          => 'TCS Smart Hiring – Graduate Trainee',
            'description'    => "TCS Smart Hiring is a structured onboarding programme for fresh graduates joining TCS as Trainee Engineers.\n\n**Training:**\n- 10-week paid foundation training at TCS iON\n- Technology tracks: Java Full Stack, Python, Cloud Native\n- Soft skills and AGILE methodology training\n\n**Eligibility:**\n- B.E./B.Tech 2024 pass-out with 60% aggregate\n- No active backlogs\n- Available for pan-India posting",
            'job_type'       => 'full-time',
            'location'       => 'Pan India',
            'is_remote'      => false,
            'stipend_salary' => '₹3.6 – 7 LPA',
            'deadline'       => now()->addDays(40),
            'skills_required'=> 'Java, Python, SQL, Communication',
            'job_url'        => 'https://tcs.com/careers/smart-hiring-2024',
            'is_active'      => true,
            'posted_at'      => now()->subDays(10),
        ]);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob2->id, 'skill_id' => $sk['Java']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob2->id, 'skill_id' => $sk['MySQL']->id, 'importance' => 'medium']);

        $extJob3 = Job::create([
            'company_id'     => $extCompanyISRO->id,
            'source_id'      => $govtSource->id,
            'source_job_id'  => 'isro-ra-2024-sc-c',
            'title'          => 'ISRO Research Associate – Space Technology',
            'description'    => "ISRO invites applications for Research Associate positions in the domain of Space Technology and Systems.\n\n**Vacancy:** Research Associate SC-C\n**Pay Level:** 10 (₹56,100 – ₹1,77,500)\n**Posting:** VSSC, Thiruvananthapuram\n\n**Qualification:**\n- M.Tech / M.E. / M.Sc. with First Class\n- Specialisation in Electronics, Computer Science, or Physics\n- Age limit: 35 years as on last date\n\n**Selection:** Written Test + Interview by Board",
            'job_type'       => 'government',
            'location'       => 'Thiruvananthapuram, Kerala',
            'is_remote'      => false,
            'stipend_salary' => '₹56,100 – ₹1,77,500 / month (Pay Level 10)',
            'deadline'       => now()->addDays(35),
            'skills_required'=> 'Electronics, Computer Science, Research, Systems Design',
            'job_url'        => 'https://isro.gov.in/recruitment/2024/ra-sc-c',
            'is_active'      => true,
            'posted_at'      => now()->subDays(4),
        ]);

        $extJob4 = Job::create([
            'company_id'     => $extCompanyGoogle->id,
            'source_id'      => $linkedinSource->id,
            'source_job_id'  => 'goog-ux-researcher-2024',
            'title'          => 'UX Research Intern – Google Assistant',
            'description'    => "Design and conduct user research studies to improve Google Assistant's multimodal experiences.\n\n**Responsibilities:**\n- Plan and run usability tests with diverse user groups\n- Synthesise findings into actionable design recommendations\n- Collaborate with Product Managers and UX Designers\n\n**Qualifications:**\n- Currently enrolled in HCI, Psychology, or Design programme\n- Experience with Figma or equivalent tools\n- Strong written and verbal communication",
            'job_type'       => 'internship',
            'location'       => 'Hyderabad, India',
            'is_remote'      => true,
            'stipend_salary' => '₹85,000 / month',
            'deadline'       => now()->addDays(28),
            'skills_required'=> 'UI/UX, Figma, User Research, Communication',
            'job_url'        => 'https://careers.google.com/jobs/results/ux-research-intern-2024',
            'is_active'      => true,
            'posted_at'      => now()->subDays(6),
        ]);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob4->id, 'skill_id' => $sk['UI/UX']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob4->id, 'skill_id' => $sk['Figma']->id, 'importance' => 'high']);

        $extJob5 = Job::create([
            'company_id'     => $extCompanyTCS->id,
            'source_id'      => $internshalaSource->id,
            'source_job_id'  => 'tcs-data-analyst-intern',
            'title'          => 'Data Analytics Intern – TCS Analytics',
            'description'    => "6-month data analytics internship working on real client data transformation projects across BFSI, Retail and Healthcare sectors.\n\n**Skills you'll use:**\n- Python (Pandas, NumPy, Matplotlib)\n- SQL for data extraction and transformation\n- Power BI / Tableau dashboarding\n- Basic machine learning models for forecasting\n\n**Eligibility:**\n- Currently enrolled in Statistics, Data Science, or CS programme\n- Basic Python and SQL knowledge",
            'job_type'       => 'internship',
            'location'       => 'Chennai, India',
            'is_remote'      => false,
            'stipend_salary' => '₹20,000 / month',
            'deadline'       => now()->addDays(18),
            'skills_required'=> 'Python, Data Analysis, SQL, Excel',
            'job_url'        => 'https://internshala.com/internship/tcs-data-analytics-2024',
            'is_active'      => true,
            'posted_at'      => now()->subDays(9),
        ]);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob5->id, 'skill_id' => $sk['Python']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob5->id, 'skill_id' => $sk['Data Analysis']->id, 'importance' => 'high']);
        JobSkill::create(['id' => (string) Str::uuid(), 'job_id' => $extJob5->id, 'skill_id' => $sk['Excel']->id, 'importance' => 'medium']);

        // ────────────────────────────────────────────────────────────────────
        // 8. APPLICATIONS WITH FULL STATUS TIMELINES
        // ────────────────────────────────────────────────────────────────────

        // Application A: Aarav → Nexus Internship (currently in interview stage)
        $appA = Application::create([
            'student_id'      => $studentA->id,
            'job_id'          => $job1->id,
            'status'          => 'interview',
            'applied_at'      => now()->subDays(8),
            'cover_letter_url'=> 'https://drive.google.com/file/d/cover_aarav_nexus',
            'expected_salary' => '₹45,000/month',
            'notes'           => 'Very excited about this opportunity. Have relevant project experience with Laravel and React.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appA->id, 'status' => 'applied',
            'changed_at' => now()->subDays(8), 'changed_by' => $studentUserA->id,
            'remarks' => 'Application submitted through InternTrack.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appA->id, 'status' => 'shortlisted',
            'changed_at' => now()->subDays(5), 'changed_by' => $companyUserA->id,
            'remarks' => 'Strong profile. Shortlisted for technical rounds.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appA->id, 'status' => 'interview',
            'changed_at' => now()->subDays(3), 'changed_by' => $companyUserA->id,
            'remarks' => 'Moving to interview stage. Scheduling Round 1 shortly.',
        ]);

        // Interview rounds for appA
        Interview::create([
            'application_id'       => $appA->id,
            'round_number'         => 1,
            'round_name'           => 'Technical Coding Round',
            'interview_type'       => 'online',
            'scheduled_at'         => now()->addDays(2),
            'interviewer'          => 'Vikram Singh, Lead Engineer',
            'location_meeting_link'=> 'https://meet.google.com/nxs-abc-123',
            'result'               => 'pending',
        ]);
        Interview::create([
            'application_id'       => $appA->id,
            'round_number'         => 2,
            'round_name'           => 'System Design Discussion',
            'interview_type'       => 'online',
            'scheduled_at'         => now()->addDays(5),
            'interviewer'          => 'Ananya Krishnan, Principal Architect',
            'location_meeting_link'=> 'https://meet.google.com/nxs-def-456',
            'result'               => 'pending',
        ]);

        // Application B: Aarav → Google STEP Internship (applied, awaiting response)
        $appB = Application::create([
            'student_id'      => $studentA->id,
            'job_id'          => $extJob1->id,
            'status'          => 'applied',
            'applied_at'      => now()->subDays(3),
            'cover_letter_url'=> 'https://drive.google.com/file/d/cover_aarav_google',
            'expected_salary' => '₹1,20,000/month',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appB->id, 'status' => 'applied',
            'changed_at' => now()->subDays(3), 'changed_by' => $studentUserA->id,
            'remarks' => 'Application submitted via InternTrack.',
        ]);

        // Application C: Aarav → TCS Graduate Trainee (rejected)
        $appC = Application::create([
            'student_id'  => $studentA->id,
            'job_id'      => $extJob2->id,
            'status'      => 'rejected',
            'applied_at'  => now()->subDays(20),
            'notes'       => 'Applied as backup option.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appC->id, 'status' => 'applied',
            'changed_at' => now()->subDays(20), 'changed_by' => $studentUserA->id,
            'remarks' => 'Application submitted.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appC->id, 'status' => 'rejected',
            'changed_at' => now()->subDays(12), 'changed_by' => $adminUser->id,
            'remarks' => 'Unfortunately not selected at this time. Please reapply next cycle.',
        ]);

        // Application D: Aarav → Nexus DevOps (selected!)
        $appD = Application::create([
            'student_id'      => $studentA->id,
            'job_id'          => $job3->id,
            'status'          => 'selected',
            'applied_at'      => now()->subDays(30),
            'cover_letter_url'=> 'https://drive.google.com/file/d/cover_aarav_devops',
            'expected_salary' => '₹15 LPA',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appD->id, 'status' => 'applied',
            'changed_at' => now()->subDays(30), 'changed_by' => $studentUserA->id,
            'remarks' => 'Application submitted.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appD->id, 'status' => 'shortlisted',
            'changed_at' => now()->subDays(27), 'changed_by' => $companyUserA->id,
            'remarks' => 'Excellent DevOps portfolio. Moving to interviews.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appD->id, 'status' => 'interview',
            'changed_at' => now()->subDays(22), 'changed_by' => $companyUserA->id,
            'remarks' => 'Interview scheduled.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appD->id, 'status' => 'selected',
            'changed_at' => now()->subDays(10), 'changed_by' => $companyUserA->id,
            'remarks' => 'Congratulations! Offer letter is being processed.',
        ]);
        Interview::create([
            'application_id'  => $appD->id,
            'round_number'    => 1,
            'round_name'      => 'Technical Infrastructure Round',
            'interview_type'  => 'video',
            'scheduled_at'    => now()->subDays(21),
            'interviewer'     => 'Rohan Kapoor, SRE Lead',
            'result'          => 'cleared',
        ]);
        Interview::create([
            'application_id'  => $appD->id,
            'round_number'    => 2,
            'round_name'      => 'HR & Culture Fit',
            'interview_type'  => 'video',
            'scheduled_at'    => now()->subDays(18),
            'interviewer'     => 'Sonia Mehta, HR Manager',
            'result'          => 'cleared',
        ]);

        // Application E: Priya → AI Fellowship (shortlisted)
        $appE = Application::create([
            'student_id'      => $studentB->id,
            'job_id'          => $job2->id,
            'status'          => 'shortlisted',
            'applied_at'      => now()->subDays(4),
            'cover_letter_url'=> 'https://drive.google.com/file/d/cover_priya_fellowship',
            'expected_salary' => '₹60,000/month',
            'notes'           => 'Published two papers on LLM inference optimization.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appE->id, 'status' => 'applied',
            'changed_at' => now()->subDays(4), 'changed_by' => $studentUserB->id,
            'remarks' => 'Application submitted with research papers.',
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appE->id, 'status' => 'shortlisted',
            'changed_at' => now()->subDays(1), 'changed_by' => $companyUserA->id,
            'remarks' => 'Impressive research background. Shortlisted for research presentation round.',
        ]);

        // Application F: Rahul → TCS Data Analytics Intern (applied)
        $appF = Application::create([
            'student_id' => $studentC->id,
            'job_id'     => $extJob5->id,
            'status'     => 'applied',
            'applied_at' => now()->subDays(1),
        ]);
        ApplicationStatusHistory::create([
            'application_id' => $appF->id, 'status' => 'applied',
            'changed_at' => now()->subDays(1), 'changed_by' => $studentUserC->id,
            'remarks' => 'Application submitted through InternTrack.',
        ]);

        // ────────────────────────────────────────────────────────────────────
        // 9. SAVED JOBS
        // ────────────────────────────────────────────────────────────────────

        foreach ([$extJob1, $job2, $extJob3, $extJob4] as $savedJob) {
            SavedJob::firstOrCreate([
                'student_id' => $studentA->id,
                'job_id'     => $savedJob->id,
            ], ['saved_at' => now()->subDays(rand(1, 5))]);
        }

        foreach ([$extJob1, $extJob5, $job3] as $savedJob) {
            SavedJob::firstOrCreate([
                'student_id' => $studentB->id,
                'job_id'     => $savedJob->id,
            ], ['saved_at' => now()->subDays(rand(1, 3))]);
        }

        // ────────────────────────────────────────────────────────────────────
        // 10. NOTIFICATIONS
        // ────────────────────────────────────────────────────────────────────

        $notifications = [
            [
                'user_id' => $studentUserA->id,
                'type'    => 'status_update',
                'title'   => 'Application Shortlisted – Nexus Innovations',
                'message' => 'Your application for "Full-Stack Engineering Intern" at Nexus Innovations has been shortlisted.',
                'data'    => ['application_id' => $appA->id],
                'is_read' => false,
            ],
            [
                'user_id' => $studentUserA->id,
                'type'    => 'interview_reminder',
                'title'   => 'Interview in 2 Days – Nexus Technical Round',
                'message' => 'Reminder: Your Technical Coding Round is scheduled with Nexus Innovations on ' . now()->addDays(2)->format('M d, Y') . '.',
                'data'    => ['application_id' => $appA->id],
                'is_read' => false,
            ],
            [
                'user_id' => $studentUserA->id,
                'type'    => 'offer',
                'title'   => '🎉 Offer Extended – Nexus Innovations DevOps',
                'message' => 'Congratulations! Nexus Innovations has extended an offer for the DevOps Engineer role.',
                'data'    => ['application_id' => $appD->id],
                'is_read' => true,
            ],
            [
                'user_id' => $studentUserB->id,
                'type'    => 'status_update',
                'title'   => 'Shortlisted – AI Research Fellowship',
                'message' => 'Your application for the AI Research Fellowship at Nexus Innovations has been shortlisted.',
                'data'    => ['application_id' => $appE->id],
                'is_read' => false,
            ],
        ];

        foreach ($notifications as $n) {
            Notification::create($n);
        }

        // ────────────────────────────────────────────────────────────────────
        // 11. ADMIN AUDIT LOGS
        // ────────────────────────────────────────────────────────────────────

        $logs = [
            [
                'admin_id'    => $admin->id,
                'action'      => 'company_verification_approved',
                'entity_type' => 'Company',
                'entity_id'   => $companyA->id,
                'description' => 'Approved verification for Nexus Innovations. Documents reviewed.',
            ],
            [
                'admin_id'    => $admin->id,
                'action'      => 'company_verification_approved',
                'entity_type' => 'Company',
                'entity_id'   => $extCompanyGoogle->id,
                'description' => 'Auto-verified Google India as well-known external organisation.',
            ],
            [
                'admin_id'    => $admin->id,
                'action'      => 'company_verification_rejected',
                'entity_type' => 'Company',
                'entity_id'   => $companyC->id,
                'description' => 'Rejected Phantom Ventures verification. Invalid documentation submitted.',
            ],
            [
                'admin_id'    => $admin->id,
                'action'      => 'job_aggregator_run',
                'entity_type' => 'JobSource',
                'entity_id'   => $linkedinSource->id,
                'description' => 'External job aggregator executed for LinkedIn Jobs source. 2 jobs ingested.',
            ],
        ];

        foreach ($logs as $log) {
            AdminLog::create($log);
        }

        $this->command->info('');
        $this->command->info('✅  InternTrack demo data seeded successfully!');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',             'admin@interntrack.dev',   'password'],
                ['Company (Verified)','company@interntrack.dev', 'password'],
                ['Company (Pending)', 'hr@starlight.com',        'password'],
                ['Company (Rejected)','jobs@phantom.co',         'password'],
                ['Company (Unverified)','info@greenpath.org',    'password'],
                ['Student (Primary)', 'student@interntrack.dev', 'password'],
                ['Student',           'priya@university.edu',    'password'],
                ['Student',           'rahul@college.ac.in',     'password'],
            ]
        );
        $this->command->info('');
        $this->command->info('Jobs seeded: 4 direct (InternTrack) + 5 external (LinkedIn, Internshala, Govt)');
        $this->command->info('Applications: 6 applications across 3 students with full status timelines');
        $this->command->info('Interviews: 4 rounds (2 pending, 2 cleared)');
        $this->command->info('Saved Jobs: 7 bookmarked entries across students');
    }
}
