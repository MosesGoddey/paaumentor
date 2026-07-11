# PAAUMENTOR — A PEER MENTORSHIP PLATFORM FOR PRINCE ABUBAKAR AUDU UNIVERSITY (PAAU), ANYIGBA

**Project Report — Chapters Three, Four and Five**

**By:** Moses Goddey Joseph (Matric No: 23CS1004)
**Department:** Computer Science
**Institution:** Prince Abubakar Audu University, Anyigba, Kogi State, Nigeria
**Supervisor:** Mr. Richard Akomodi

---

# CHAPTER THREE

# SYSTEM ANALYSIS AND DESIGN

## 3.1 Introduction

This chapter presents the analysis of the existing mentorship arrangement at Prince Abubakar Audu University and the design of the proposed system, PAAUMENTOR. It begins with the methodology adopted for the development of the system, examines the weaknesses of the present informal mentorship practice, and states the requirements — functional and non-functional — that the new system must satisfy. It then presents the design of the proposed system: its overall architecture, the actors and their use cases, the flow of data through the system, the database design, and the design of the system's inputs and outputs. The design produced in this chapter forms the blueprint from which the implementation described in Chapter Four was carried out.

## 3.2 Research Methodology

### 3.2.1 Fact-Finding Techniques

Information about the existing mentorship practice and the requirements of the proposed system was gathered using the following techniques:

1. **Observation** — The researcher, as a student of the university, directly observed how students seek academic and career guidance: informally approaching senior colleagues, joining unstructured WhatsApp groups, or relying on chance encounters with lecturers and alumni.
2. **Interviews and informal discussions** — Discussions were held with fellow students, senior students and recent graduates to understand how mentoring relationships currently form, what obstacles exist, and what features they would value in a dedicated platform.
3. **Review of existing systems and literature** — Established mentorship and e-learning platforms (reviewed in Chapter Two) were studied to identify proven features — mentor directories, structured curricula, verifiable credentials — and to identify gaps that a university-specific solution should fill.

### 3.2.2 Software Development Methodology

The system was developed using the **incremental (iterative) model** of the Software Development Life Cycle (SDLC). Under this model the system is analysed and designed as a whole, but built and tested in small, working increments — authentication first, then mentorship management, then learning paths, then the certification pipeline, and so on — with each increment integrated into the growing system and tested before the next is begun.

The incremental model was preferred over the classical waterfall model for the following reasons:

1. The project has a large feature set (over a dozen modules); building it in increments allowed a usable core to exist early, reducing risk.
2. Requirements for later modules (for example, the mentor-upgrade pipeline and hackathon module) were refined based on experience gained while building earlier modules.
3. Each increment could be tested immediately, so defects were discovered close to the point at which they were introduced.

> **Figure 3.1:** The incremental development model adopted for the project *(insert diagram)*

## 3.3 Analysis of the Existing System

At present, mentorship at Prince Abubakar Audu University is **informal and unstructured**. A student who desires guidance must personally identify and approach a senior student, lecturer or alumnus, usually through personal networks, departmental associations or social-media groups. There is no central register of willing mentors, no defined curriculum for what a mentee should learn, no record of the mentoring that takes place, and no recognised evidence that a student has acquired a skill under mentorship.

### 3.3.1 Weaknesses of the Existing System

The analysis identified the following weaknesses in the existing arrangement:

1. **Difficulty of discovery** — Mentees have no reliable way of finding mentors with the skills they need; matching depends on luck and personal connections, which disadvantages students with smaller networks.
2. **Absence of structure** — Even where a mentoring relationship forms, there is no defined learning path, milestones or assessment; progress is neither planned nor measurable.
3. **No records or accountability** — Sessions, advice and progress are untracked; if a relationship lapses, nothing is preserved.
4. **No verifiable outcome** — A student who genuinely acquires a skill through mentorship has no credible, verifiable evidence of it to show employers or admission panels.
5. **Mentor pool does not grow** — There is no pathway by which successful mentees become mentors, so the burden rests permanently on a small set of willing seniors.
6. **No quality control** — Anyone may present themselves as knowledgeable; there is no vetting of mentors and no measure of their effectiveness.
7. **Communication fragmentation** — Discussions are scattered across personal chats and social media, mixed with unrelated content, and are not organised around learning.

### 3.3.2 The Proposed System

The proposed system, **PAAUMENTOR**, is a web-based peer mentorship platform that replaces the informal arrangement with a structured, recorded and verifiable process. In the proposed system:

- Students register as **mentees** and can browse a directory of **verified mentors** (senior students and alumni), or use an **AI-powered matching** facility that recommends the most suitable mentors from a free-text description of the mentee's goals.
- Mentors publish **structured learning paths** consisting of modules and assessable tasks; mentees follow the path, submit work, and receive grades and feedback.
- Mentoring interaction takes place on the platform itself: **one-to-one messaging**, **video/voice sessions** conducted in an in-app meeting room, **group sessions**, **study groups** with group chat, a **shared resource library**, and a **skill-exchange marketplace**.
- On completing a learning path, the mentee passes through a **multi-stage certification pipeline** — an AI-generated assessment (pass mark 70 %), a written reflection by the mentor, and approval by an independent **verifier** — after which a certificate bearing a unique identifier and a **QR code** is issued. Anyone may verify the certificate publicly without logging in.
- Accomplished mentees may apply through a structured **mentor-upgrade pathway** to become mentors themselves, making the community self-sustaining.
- **Administrators** oversee the platform (statistics, user verification, suspension) and **verifiers** provide independent quality control over mentor approval and certificate issuance.

### 3.3.3 Advantages of the Proposed System

1. Centralised, searchable mentor discovery, enhanced by artificial intelligence.
2. Structured, measurable learning with defined tasks, grading and progress tracking.
3. Complete records of sessions, messages, submissions and outcomes.
4. Credible, publicly verifiable certification of skills acquired.
5. A self-sustaining mentor pool through the upgrade pathway, with earned performance tiers (Junior, Senior, Lead) that reward genuine mentoring impact.
6. Built-in quality control through verification and role-based oversight.
7. All communication and collaboration in one purpose-built place, accessible from any device.

## 3.4 Requirements Specification

### 3.4.1 Functional Requirements

The system shall:

1. Allow users to register and log in with either their email address or their student ID, and to recover forgotten passwords by email.
2. Support five user roles — mentee, mentor, alumni, administrator and verifier — and restrict every protected function to the appropriate role(s).
3. Allow mentees to browse verified mentors, view their profiles, skills, ratings and earned performance tier, and send mentorship requests which mentors may accept or decline.
4. Provide AI-powered mentor matching that ranks suitable mentors from a mentee's stated goals.
5. Allow mentors to create learning paths (manually or with AI assistance) comprising modules and tasks; allow mentees to submit task work; allow mentors to grade submissions with feedback.
6. Allow the scheduling and conduct of one-to-one mentorship sessions (video, voice or chat) and multi-participant group sessions, with in-app meeting rooms and recorded outcomes.
7. Provide one-to-one messaging with typing indicators and read receipts, study groups with group chat, a shared resource library, and a skill-exchange marketplace.
8. Implement the certification pipeline: AI-generated 30-question assessment with a 70 % pass mark and tab-switch (malpractice) detection, mentor reflection, verifier approval, and issuance of a PDF certificate with a unique ID and QR code that is publicly verifiable.
9. Implement the mentor-upgrade pipeline: eligibility checking, AI-generated upgrade assessment, mentor recommendation and administrator approval.
10. Support hackathon events: creation, team formation, project submission, judge assignment, scoring, leaderboards and result publication.
11. Provide an AI study assistant for academic questions, and a public "Mentor AI" help widget available even to unauthenticated visitors.
12. Notify users in-app of relevant events (requests, responses, grading, certificates, incoming calls, invitations) with unread counts.
13. Provide administrators with platform statistics and user-management facilities, and verifiers with queues of mentor portfolios and certificate requests awaiting review.
14. Present a public landing page with platform statistics and a data-driven showcase of the top-rated, highest-tier mentors.

### 3.4.2 Non-Functional Requirements

1. **Security** — Passwords must be stored hashed; all state-changing requests must be CSRF-protected; database access must be injection-safe; users must only act on resources they are authorised to access.
2. **Usability** — The interface must be simple, consistent and fully responsive on desktop and mobile, with a dark-mode option; destructive or session-ending actions (such as sign-out) must ask for confirmation.
3. **Reliability** — Failure of the external AI service must not crash the system; AI-dependent features must degrade gracefully.
4. **Maintainability** — The code must follow the Model–View–Controller pattern with the database schema kept in version-controlled migrations, so the system can be reproduced and extended.
5. **Performance** — Ordinary pages must load within a few seconds on a typical university internet connection; message polling must be lightweight.
6. **Portability** — The system must run on commodity hardware under Windows or Linux using free, open-source software.

## 3.5 System Design

### 3.5.1 Architectural Design

PAAUMENTOR is designed as a **three-tier, server-rendered web application** following the **Model–View–Controller (MVC)** architectural pattern:

- **Presentation tier** — Blade templates rendered to HTML, styled with custom CSS and made interactive with vanilla JavaScript (charts, chat polling, theme toggling, meeting-room embedding).
- **Application tier** — Laravel controllers implement the use cases; middleware enforces authentication and role-based access; dedicated service classes encapsulate integration with the external AI model; policies enforce per-resource authorisation.
- **Data tier** — A MySQL relational database accessed exclusively through the Eloquent object-relational mapper.

Two external services are integrated at the application tier: the **Google Gemini** generative AI model (mentor matching, curriculum generation, assessments, study assistant) accessed over REST, and the **Jitsi Meet** conferencing service, embedded in the browser through its iFrame API to host the media streams of video and voice sessions.

> **Figure 3.2:** Architecture of the proposed system *(insert diagram)*

```
 ┌────────────────────────────────────────────────────────────┐
 │                        CLIENT (Browser)                    │
 │   Blade-rendered HTML · CSS · JavaScript · Jitsi iFrame    │
 └───────────────▲────────────────────────────▲───────────────┘
                 │ HTTP/HTTPS                 │ WebRTC media
 ┌───────────────┴───────────────┐   ┌────────┴───────────────┐
 │      LARAVEL APPLICATION      │   │   Jitsi Meet service   │
 │  Routes → Middleware →        │   └────────────────────────┘
 │  Controllers → Services       │   ┌────────────────────────┐
 │  (AiService) → Models         ├──►│  Google Gemini API     │
 └───────────────┬───────────────┘   └────────────────────────┘
                 │ Eloquent ORM
 ┌───────────────▼───────────────┐
 │        MySQL DATABASE         │
 │  users · mentorships · paths  │
 │  sessions · certificates · …  │
 └───────────────────────────────┘
```

### 3.5.2 Use-Case Design

The actors of the system and their principal use cases are summarised below.

**Table 3.1: Actors and Use Cases**

| Actor | Principal Use Cases |
|---|---|
| Guest (unauthenticated) | View landing page and featured mentors; register; log in; verify a certificate via QR code; ask the public Mentor AI widget questions |
| Mentee | Browse/match mentors; request mentorship; follow learning paths; submit tasks; schedule/attend sessions and group sessions; chat; join study groups; share resources; exchange skills; take certification assessments; rate mentors; apply for mentor upgrade; use AI study buddy; join hackathons |
| Mentor | Accept/decline mentees; create learning paths (manual or AI-assisted); grade submissions; conduct sessions and group sessions; write certification reflections; recommend mentees for upgrade; coach hackathon teams |
| Alumni | As mentor (external/graduate mentors) |
| Verifier | Review and approve/reject mentor portfolios; review and approve/reject certificate requests |
| Administrator | View platform statistics; verify users; suspend/reactivate users; create verifier accounts; approve mentor upgrades; manage hackathons and judges |

> **Figure 3.3:** Use-case diagram of PAAUMENTOR *(insert diagram)*

### 3.5.3 Data-Flow Design

At the highest level (context/Level-0 diagram), the system exchanges data with five external entities: the mentee, the mentor/alumnus, the administrator, the verifier and the external AI service.

> **Figure 3.4:** Context (Level-0) data-flow diagram *(insert diagram)*

The Level-1 decomposition identifies the major processes:

1. **P1 Account Management** — registration, login (email or student ID), password reset; reads/writes the *users* store.
2. **P2 Mentorship Management** — discovery, AI matching, requests and responses; writes the *mentorships* store; calls the AI service.
3. **P3 Learning Management** — path creation (with AI assistance), task submission and grading; writes *learning_paths*, *learning_modules*, *learning_tasks*, *task_submissions*.
4. **P4 Communication** — messaging, study groups, session and group-session scheduling and rooms; writes *conversations*, *messages*, *sessions*, *group_sessions*, *study_groups*.
5. **P5 Certification** — assessment generation and taking, mentor reflection, verifier decision, certificate issuance and public verification; writes *certificate_requests*, *assessments*, *certificates*; calls the AI service.
6. **P6 Community Growth** — mentor-upgrade applications, upgrade assessments, recommendations and approvals; skill exchange; hackathons.
7. **P7 Administration** — statistics, verification, suspension, verifier creation; notifications are produced by all processes and consumed by users.

> **Figure 3.5:** Level-1 data-flow diagram *(insert diagram)*

### 3.5.4 Process Design — Key Workflows

Two workflows define the character of the system and are designed as follows.

**(a) Certification workflow**

```
Mentee completes all tasks on a learning path (all graded)
        │
        ▼
System creates a certificate request and asks the AI service
to generate a 30-question assessment for the path
        │
        ▼
Mentee sits the assessment (tab-switching is detected and logged)
        │
   score ≥ 70 %? ──No──► Mentee may retake after regeneration
        │Yes
        ▼
Mentor writes a reflection on the mentee's performance
        │
        ▼
Verifier reviews the request ──Reject──► Mentee/mentor notified
        │Approve
        ▼
Certificate issued: unique ID + QR code + downloadable PDF
        │
        ▼
Anyone can verify the certificate publicly via the QR link
```

**(b) Mentor-upgrade workflow**

```
Mentee applies (must have ≥ 5 completed sessions, ≥ 1 certificate,
≥ 1 completed learning path, and an active mentor)
        │
        ▼
AI-generated upgrade assessment ── fail ──► may retry
        │ pass
        ▼
Current mentor writes a recommendation
        │
        ▼
Administrator approves ──► user's role becomes "mentor"
                            (initial tier: Junior Mentor)
```

> **Figure 3.6:** System flowchart of the certification pipeline *(insert diagram)*

### 3.5.5 Database Design

The database is relational and normalised to the third normal form. It comprises over thirty tables; the principal entities and their relationships are:

- A **user** (one) may participate in many **mentorships**, as mentor or as mentee (many-to-many between users, reified by the *mentorships* table with a status).
- A **mentorship** (one) has many **sessions**; a **user** (one) may host many **group sessions**, each with many members (many-to-many via *group_session_members*).
- A **user** (mentor, one) creates many **learning paths**; a path (one) has many **modules**; a module (one) has many **tasks**; a task (one) receives many **submissions**, each belonging to one mentee.
- A completed path/mentee pair produces one **certificate request**, which owns one **assessment** (with many questions and attempts) and, on approval, one **certificate**.
- **Conversations** link two users and contain many **messages**; **study groups** have many members and many group messages; **skill exchanges** receive many requests; **hackathons** have many teams, submissions and scores; **ratings** and **notifications** each belong to one user.

**Table 3.2: Principal Database Tables**

| Table | Purpose |
|---|---|
| `users` | All accounts: name, email, student ID, hashed password, role, department, level, bio, avatar, verification/active flags, mentor status |
| `skills` | Skill catalogue linked to users |
| `mentorships` | Mentor–mentee relationships with status (pending/active/rejected/completed) |
| `sessions` | One-to-one mentorship sessions (video/voice/chat) with schedule, status and call outcome |
| `group_sessions` / `group_session_members` | Host-led multi-participant sessions with room identifier, capacity, membership roles and join times |
| `learning_paths` / `learning_modules` / `learning_tasks` | Structured curricula |
| `task_submissions` | Mentee submissions with grades and feedback |
| `conversations` / `messages` | One-to-one messaging |
| `study_groups` / `study_group_members` / `study_group_messages` | Group study spaces and chat |
| `shared_resources` / `resources` | Shared learning-materials library |
| `skill_exchanges` / `skill_exchange_requests` | Peer-to-peer skill marketplace |
| `certificate_requests` | Certification pipeline records, including mentor reflections |
| `assessments` / `assessment_questions` / `assessment_attempts` | AI-generated certification assessments |
| `certificates` | Issued certificates with unique IDs and QR verification data |
| `mentor_upgrade_requests` | Mentor-upgrade applications |
| `upgrade_assessments` (+ questions, attempts) | AI-generated upgrade assessments |
| `hackathons` / `hackathon_teams` / `hackathon_submissions` / `hackathon_scores` | Hackathon management |
| `ratings` | Mentor ratings and reviews |
| `notifications` | In-app notifications |

> **Figure 3.7:** Entity-relationship diagram of the PAAUMENTOR database *(insert ERD)*

**Table 3.3: Design of the `users` Table**

| Field | Data Type | Constraint / Description |
|---|---|---|
| id | BIGINT | Primary key, auto-increment |
| first_name / last_name | VARCHAR | Not null |
| email | VARCHAR | Unique, not null |
| student_id | VARCHAR | Unique, nullable (alternative login) |
| password | VARCHAR | Bcrypt-hashed, not null |
| role | ENUM | mentee, mentor, alumni, admin, verifier (default mentee) |
| department / level | VARCHAR | Nullable |
| bio | TEXT | Nullable |
| phone / avatar / availability | VARCHAR | Nullable |
| is_verified / is_active | BOOLEAN | Defaults false / true |
| mentor_status | VARCHAR | Mentor-approval state (e.g. pending, active) |
| created_at / updated_at | TIMESTAMP | Record timestamps |

### 3.5.6 Input Design

Inputs are captured through validated web forms. The principal inputs are the registration form (names, email, student ID, password, department, level), the login form (email *or* student ID, password), the mentorship-request and goal-description inputs, the learning-path builder (including the topic/level/duration inputs to the AI generator), task-submission forms (text and file), grading forms (score, feedback), session and group-session scheduling forms (title, type, date/time, invitees), chat inputs, assessment answer sheets, reflection and recommendation forms, and administrative controls. Every form is server-side validated, and invalid input is rejected with a descriptive message beside the offending field.

### 3.5.7 Output Design

The principal outputs are: role-specific dashboards with statistics and charts; the mentor directory and ranked AI match results; learning-path progress views; chat and group-chat threads; session and group-session lists and in-app meeting rooms; assessment results with scores; the certificate — displayed on screen and downloadable as a PDF bearing a QR code; the public certificate-verification page; hackathon leaderboards; notification lists; and the administrator's platform-statistics dashboard.

## 3.6 Summary of the Chapter

This chapter analysed the informal mentorship arrangement currently obtainable at Prince Abubakar Audu University and identified its weaknesses: poor discovery, absence of structure, no records, no verifiable outcomes, a static mentor pool and no quality control. A web-based replacement was proposed and its requirements specified. The design of the proposed system was then presented — a three-tier MVC web application with an integrated AI service and embedded conferencing, its actors and use cases, its data flows and key workflows, its normalised relational database, and its input and output designs. The next chapter describes how this design was implemented and tested.

---

# CHAPTER FOUR

# SYSTEM IMPLEMENTATION, TESTING AND RESULTS

## 4.1 Introduction

This chapter presents the actual implementation of the PAAUMENTOR peer mentorship platform whose analysis and design were discussed in Chapter Three. It describes the software and hardware tools used in building the system, the implementation of the database, the development of the individual functional modules, and the manner in which the various subsystems were integrated into a single working web application.

The chapter further discusses the testing strategy adopted to validate the system, presents representative test cases together with their outcomes, and discusses the results obtained from running the completed system. The objective is to demonstrate that the design specifications have been faithfully translated into a functional, reliable and secure software product that meets the requirements established earlier in the work.

## 4.2 Choice of Development Tools

The system was developed as a server-rendered web application using the Laravel framework. The tools were selected on the basis of maturity, availability of documentation, cost (all tools are free and open-source or offer free tiers), and suitability for a database-driven, multi-user platform. Table 4.1 summarises the tools used and the role each played in the project.

**Table 4.1: Development Tools and Technologies**

| Layer / Concern | Technology Used | Purpose in the System |
|---|---|---|
| Backend framework | Laravel 11 (PHP 8.2+) | Application logic, routing, ORM, authentication, validation |
| Programming language | PHP 8.2 | Server-side scripting |
| Database | MySQL (via MariaDB) | Persistent storage of all application data |
| Web/templating engine | Blade | Generation of dynamic HTML pages |
| Frontend styling | Custom CSS (`public/css/style.css`) | Visual presentation, responsive layout, dark mode |
| Frontend interactivity | Vanilla JavaScript (`public/js/app.js`) | Charts, theme toggling, live chat polling, client-side validation |
| Artificial Intelligence | Google Gemini 2.5 Flash (REST API) | Mentor matching, learning-path generation, study assistant, assessment generation, public help widget |
| Video/voice conferencing | Jitsi Meet (iFrame External API) | In-app meeting rooms for one-to-one and group sessions |
| PDF generation | barryvdh/laravel-dompdf | Certificate document rendering |
| QR code generation | chillerlan/php-qrcode | Embedding verifiable QR codes on certificates |
| Local development server | XAMPP (Apache, MySQL) / `php artisan serve` | Hosting the application during development |
| Version control | Git | Source-code management and change tracking |
| Dependency management | Composer (PHP), npm (assets) | Library and package management |

### 4.2.1 Justification for Laravel

Laravel was chosen as the core framework for the following reasons:

1. **Model–View–Controller (MVC) architecture** — Laravel enforces a clean separation between data (Models), presentation (Blade Views) and request handling (Controllers), which made the large feature set of PAAUMENTOR easier to organise and maintain.
2. **Eloquent ORM** — Database tables are mapped to PHP objects, allowing relationships (one-to-many, many-to-many) to be expressed in code and reducing the amount of raw SQL required.
3. **Built-in security** — Cross-Site Request Forgery (CSRF) protection, password hashing (Bcrypt), SQL-injection-safe query building and input validation are provided out of the box.
4. **Migrations and seeders** — The database schema is defined in version-controlled migration files, making the structure reproducible on any machine, and seeders allow demonstration data to be generated automatically.
5. **Middleware** — Route protection by authentication and role (e.g. `auth`, `admin`) is handled cleanly through middleware.

## 4.3 System Requirements

For the system to be deployed and operated effectively, the minimum hardware and software requirements shown in Tables 4.2 and 4.3 should be met.

**Table 4.2: Minimum Hardware Requirements**

| Component | Minimum Specification |
|---|---|
| Processor | Intel Core i3 (2.0 GHz) or equivalent |
| Memory (RAM) | 4 GB |
| Storage | 2 GB of free disk space |
| Display | 1366 × 768 resolution |
| Network | Internet connection (required for AI features and video sessions) |

**Table 4.3: Software Requirements**

| Software | Specification |
|---|---|
| Operating System | Windows 10/11, Linux, or macOS |
| Web Server | Apache (via XAMPP) or PHP built-in server |
| Runtime | PHP 8.2 or higher |
| Database | MySQL 8.0 / MariaDB 10.4 or higher |
| Web Browser | Google Chrome, Mozilla Firefox, or Microsoft Edge (current versions) |
| Dependency Managers | Composer 2.x, Node.js with npm |

## 4.4 Database Implementation

The database, named `paaumentor`, was implemented in MySQL and created entirely through Laravel migration files, which guarantee that the schema can be reproduced identically on any installation. The database comprises over thirty interrelated tables that store users, mentorship relationships, learning content, communications, group sessions, certificates, assessments and platform activities, exactly as designed in Section 3.5.5. A database seeder generates realistic demonstration data — accounts of every role, mentorships, learning paths with graded submissions, sessions and certificates — so that the complete system can be demonstrated immediately after installation.

> **Figure 4.1:** Entity-relationship diagram of the implemented database *(insert ERD here)*

## 4.5 System Implementation (Module Implementation)

The functionality of PAAUMENTOR was implemented as a collection of cooperating modules, each handled by a dedicated controller and supported by one or more Eloquent models and Blade views. The major modules are described below.

### 4.5.1 Authentication and User Management Module

This module governs registration, login, logout and password recovery. A distinctive feature is that users may log in either with their **email address** or with their **student ID**, which is convenient for students who readily remember their matriculation numbers. Passwords are securely hashed using Bcrypt, and the "forgot password" workflow issues a tokenised reset link by email. Five user roles are supported — mentee, mentor, alumni, administrator and verifier — and access to protected areas is enforced by authentication and role middleware.

> **Figure 4.2:** Login page *(insert screenshot `docs/screenshots/02-login.png`)*

### 4.5.2 Dashboard Module

After login, each user is presented with a role-specific dashboard summarising relevant information — for a mentee, active mentors, learning progress and upcoming sessions; for a mentor, mentee requests, upcoming one-to-one and group sessions, and grading tasks; for an administrator, platform-wide statistics. Progress and statistics are visualised with charts rendered in JavaScript.

### 4.5.3 Mentor Discovery and Matching Module

Mentees can browse a directory of verified mentors and, crucially, use the **AI-powered smart-matching** feature. The mentee enters their goals and interests, and the system sends these together with the profiles of available mentors to the Google Gemini model, which returns a ranked list of the most suitable mentors, each accompanied by a one-sentence justification. Mentees may then send mentorship requests, which mentors accept or decline.

To help mentees judge credibility at a glance, the system also classifies every mentor into a **performance tier** that is computed automatically from the number of mentees they have successfully certified: a mentor with fewer than five certified mentees is a *Junior Mentor*, five or more makes a *Senior Mentor*, and fifteen or more a *Lead Mentor*. The tier is therefore an earned, data-driven badge rather than a self-declared title, and it is used both to rank mentors in the directory and to feature the most accomplished mentors on the public landing page (Section 4.5.15).

### 4.5.4 Learning Path Module

Mentors create structured learning paths consisting of modules and assessable tasks. To accelerate this, the system offers **AI-assisted generation**: given a topic, learner level and duration in weeks, Gemini returns a complete, structured curriculum (3–5 modules, each with 2–4 practical tasks) as JSON, which the mentor can review and save. Mentees follow the path, submit work for each task, and mentors grade the submissions and provide feedback.

### 4.5.5 Session Management Module

This module allows mentors and mentees to schedule and conduct one-to-one mentorship sessions in video, voice or chat form. Video and voice sessions are conducted **inside the application** in a dedicated session room: the room page embeds the Jitsi Meet conferencing service through its iFrame External API, so both participants join the same uniquely named meeting room without leaving the platform or installing any software. The incoming participant is alerted through the notification system, and the session record captures the outcome — sessions carry a status (scheduled, completed, cancelled) and a call outcome (answered, missed) — enabling accurate tracking of mentoring activity, which in turn feeds the mentor-upgrade eligibility criteria. When a participant hangs up, the session is automatically marked as completed.

### 4.5.6 Group Session Module

Beyond one-to-one meetings, any user may host a **group session** — a scheduled video or voice meeting with multiple invited participants (up to a configurable maximum, fifty by default). The host selects invitees from their active mentorship connections; each invitee receives a notification of the invitation. Every group session is given a unique room identifier, and participants join the same embedded conference room from the group-sessions page. The system records each member's join time, tracks the session's life cycle (scheduled → in progress → completed) and computes the session's duration automatically when it ends. This module supports use cases such as a mentor tutoring several mentees at once or a study cohort holding a scheduled review meeting.

### 4.5.7 Messaging and Study Groups Module

Real-time-style one-to-one messaging is provided between connected users, implemented through periodic client-side polling with typing indicators and read receipts. Study groups extend this to many-to-many collaboration, with a shared group chat for collective learning.

### 4.5.8 Certificate Pipeline Module

This is one of the most significant modules. When a mentee completes every task on a learning path and the mentor grades them, the system invokes Gemini to generate a **30-question assessment**. The mentee must score at least **70 %** to pass. The mentor then writes a reflection on the mentee's performance, after which a **verifier** reviews the request and, on approval, a certificate is issued. Each certificate carries a unique identifier and a **QR code** that links to a public verification page, allowing third parties to confirm authenticity without logging in. The certificate document itself is rendered as a downloadable PDF.

> **Figure 4.3:** Issued certificate with QR code *(insert screenshot `docs/screenshots/05-certificate.png`)*

### 4.5.9 Mentor-Upgrade Module

To grow the pool of mentors organically, an eligible mentee (one who has completed at least five sessions, earned at least one certificate, completed at least one learning path and has an active mentor) may apply to become a mentor. The application triggers an AI-generated upgrade assessment; on passing, the mentee's current mentor writes a recommendation and an administrator gives final approval, after which the user's role is upgraded.

### 4.5.10 Skill Exchange Module

This module implements a peer-to-peer marketplace in which students offer a skill they possess in exchange for a skill they wish to learn. Requests can be sent, accepted and converted into a direct chat, fostering reciprocal, non-hierarchical learning.

### 4.5.11 Hackathon Module

PAAUMENTOR supports the organisation of hackathons: events with team creation and joining, project submission, judge assignment, scoring, leaderboards and result publication. Volunteer coaching by mentors is also supported.

### 4.5.12 AI Study Buddy and Mentor AI Widget Module

Two conversational AI facilities are provided. The **AI Study Buddy** is an always-available assistant, powered by Gemini, that answers authenticated students' academic questions in a conversational interface, retaining short-term conversation history for context and politely redirecting non-academic queries. In addition, a **Mentor AI chat widget** is available on the public pages (including the landing and login pages), so that even visitors who have not yet registered can ask questions about the platform and receive guided answers — serving both as a help desk and as an onboarding aid.

### 4.5.13 Administration and Verification Module

Administrators manage the platform: viewing aggregate statistics (total users, sessions, certificates, top skills), verifying and suspending users, and creating verifier accounts. Verifiers form a specialised role responsible for approving mentor portfolios and reviewing certificate requests, thereby introducing a quality-control checkpoint into the system.

### 4.5.14 Notification Module

A cross-cutting in-app notification system informs users of relevant events — mentorship responses, new messages, grading, certificate issuance, incoming calls, group-session invitations, and so on — with unread counts and a "mark all as read" facility. Incoming calls are surfaced through short-interval polling of a pending-call endpoint, so a user anywhere in the application is alerted when a session partner starts a call.

### 4.5.15 Public Landing Page and Featured-Mentors Module

The platform presents a public landing page (accessible without logging in) that serves as the entry point for prospective users. In addition to the hero banner, platform statistics and a summary of features, the landing page includes a **"Meet Our Mentors"** showcase that displays the platform's most accomplished mentors. This feature was inspired by the profile-presentation style of established commercial mentorship marketplaces, but adapted to the free, university context of PAAUMENTOR.

The showcase is **data-driven**: rather than listing hard-coded names, it queries the database for verified, active mentors, ranks them by their earned performance tier (Lead, then Senior, then Junior) and, within a tier, by their average rating, and displays the top mentors as cards. Each card shows the mentor's photograph (or their initials where no photograph is available), an earned tier badge (for example, *Senior Mentor*), name, role and department, average star rating with the number of reviews, and a sample of their skills. A call-to-action button invites visitors to sign in and connect. Because the section is generated from live data, it remains current automatically as mentors gain experience, and it gracefully hides itself when no verified mentors yet exist. This feature strengthens the platform's credibility to first-time visitors by foregrounding genuine, accomplished mentors as social proof.

> **Figure 4.4:** Landing-page "Meet Our Mentors" showcase *(insert screenshot)*

### 4.5.16 Secure Sign-Out Confirmation

To prevent accidental loss of session and to follow good usability practice, the sign-out action was enhanced with a confirmation step. When a user clicks **Sign Out** from any part of the application, the browser presents a confirmation dialog ("Are you sure you want to sign out?") that must be acknowledged before the session is terminated. This guards against unintentional logouts — for example, when a user clicks the control by mistake — while the actual logout itself remains a secure, CSRF-protected POST request.

## 4.6 System Testing

Testing was carried out to verify that each module behaves according to specification and that the modules work correctly together. A combination of testing approaches was adopted.

### 4.6.1 Testing Approaches

1. **Unit Testing** — Individual functions and methods (for example, validation rules, grade calculation, and JSON extraction from AI responses) were tested in isolation.
2. **Integration Testing** — Interactions between modules were tested, such as the flow from task completion, through assessment generation, to certificate issuance.
3. **System Testing** — The complete application was exercised end-to-end to confirm that all features operate together correctly.
4. **User Acceptance Testing (UAT)** — Representative users (students) interacted with the system to confirm that it met their expectations and was usable.

### 4.6.2 Test Cases and Results

Table 4.4 presents representative test cases used to validate core functionality, together with the expected and actual outcomes.

**Table 4.4: Selected Test Cases**

| ID | Test Case | Input | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|
| TC-01 | Login with email | Valid email + password | User authenticated, redirected to dashboard | As expected | Pass |
| TC-02 | Login with student ID | Valid student ID + password | User authenticated, redirected to dashboard | As expected | Pass |
| TC-03 | Login with wrong password | Valid email + wrong password | Error message, login refused | As expected | Pass |
| TC-04 | Register new mentee | Valid registration form | Account created, user logged in | As expected | Pass |
| TC-05 | Access admin area as mentee | Mentee navigates to `/admin` | Access denied (403) | As expected | Pass |
| TC-06 | AI mentor matching | Mentee goals + mentor list | Ranked list of suitable mentors returned | As expected | Pass |
| TC-07 | AI learning-path generation | Topic, level, weeks | Structured modules and tasks generated | As expected | Pass |
| TC-08 | Submit task | Task submission text/file | Submission recorded, awaiting grading | As expected | Pass |
| TC-09 | Grade submission | Mentor enters score + feedback | Grade saved, mentee notified | As expected | Pass |
| TC-10 | Certificate assessment (pass) | Score ≥ 70 % | Eligible for certificate issuance | As expected | Pass |
| TC-11 | Certificate assessment (fail) | Score < 70 % | Certificate withheld, retake allowed | As expected | Pass |
| TC-12 | Public certificate verification | Valid certificate ID via QR | Certificate details displayed, no login required | As expected | Pass |
| TC-13 | Verify invalid certificate ID | Non-existent certificate ID | "Certificate not found" message | As expected | Pass |
| TC-14 | Send message | Message text in conversation | Message delivered, appears in real time | As expected | Pass |
| TC-15 | Join session room | Participant opens session room | Embedded conference loads; both parties join same room | As expected | Pass |
| TC-16 | Session room access control | Non-participant opens room URL | Access denied (403) | As expected | Pass |
| TC-17 | Create group session | Title, type, date, invitees | Session created; invitees notified | As expected | Pass |
| TC-18 | Complete group session | Participant ends the session | Status set to completed; duration computed | As expected | Pass |
| TC-19 | Mentor-upgrade eligibility | Mentee not meeting criteria | Application blocked with reason | As expected | Pass |
| TC-20 | Suspend user (admin) | Admin toggles user status | User deactivated, cannot log in | As expected | Pass |
| TC-21 | AI Study Buddy query | Academic question | Relevant educational answer returned | As expected | Pass |
| TC-22 | Mentor AI widget (guest) | Question from landing page | Helpful answer returned without login | As expected | Pass |
| TC-23 | Form validation | Empty required field | Validation error shown, no save | As expected | Pass |
| TC-24 | Featured mentors on landing page | Visit landing page as guest | Top verified mentors displayed, ranked by tier and rating | As expected | Pass |
| TC-25 | Mentor tier classification | Mentor with ≥ 5 certified mentees | Tier shown as "Senior Mentor" | As expected | Pass |
| TC-26 | Sign-out confirmation | Click "Sign Out", then Cancel | Logout aborted, user remains signed in | As expected | Pass |
| TC-27 | Sign-out confirmation | Click "Sign Out", then OK | Session terminated, redirected to landing page | As expected | Pass |

### 4.6.3 Discussion of Test Results

All representative test cases produced the expected outcomes, indicating that the core functional requirements of the system were correctly implemented. Validation logic successfully rejected malformed input, role-based access control correctly prevented unauthorised access (including access to session rooms by non-participants), and the AI-dependent features returned usable results when a valid Gemini API key and internet connection were available. Where the external AI service was momentarily unavailable, the system degraded gracefully — for example, returning an empty result set rather than crashing — which confirmed the robustness of the error-handling logic.

## 4.7 Results and Discussion

The completed PAAUMENTOR system successfully realises all the objectives set out at the beginning of the project. It provides a single, integrated platform on which students of Prince Abubakar Audu University can find mentors, follow structured learning paths, communicate, hold video and voice sessions individually and in groups, collaborate in study groups, exchange skills, participate in hackathons, and earn verifiable certificates.

Several outcomes are worth highlighting:

1. **Effective mentor–mentee matching.** The integration of the Gemini AI model produced relevant mentor recommendations from free-text descriptions of mentee goals, demonstrating that artificial intelligence can meaningfully reduce the friction of finding an appropriate mentor.
2. **A rigorous, trustworthy certification pipeline.** By combining automated assessment, human mentor reflection and independent verifier approval, the system issues certificates whose authenticity can be checked publicly through a QR code. This addresses the common problem of unverifiable informal learning.
3. **Complete in-app mentoring interaction.** With embedded video/voice session rooms for both one-to-one and group sessions, mentoring conversations no longer need to migrate to external applications; scheduling, alerting, conducting and recording the outcome of a session all happen within the platform.
4. **A self-sustaining mentorship ecosystem.** The mentor-upgrade pathway enables successful mentees to become mentors, allowing the community to grow without continual external intervention.
5. **Responsive, accessible interface.** The interface is fully responsive and includes a dark-mode option, making the platform usable across devices and lighting conditions.
6. **Credible first impression through earned reputation.** The data-driven featured-mentors showcase on the landing page surfaces genuinely accomplished mentors (ranked by an earned performance tier and rating), providing social proof that encourages prospective students to register, while the public Mentor AI widget answers visitors' questions before they commit to registering.

The system therefore demonstrates that a modern, AI-augmented mentorship platform can be built using accessible, low-cost, open-source technologies suited to the Nigerian university context.

## 4.8 System Security

Several measures were implemented to protect the system and its data:

- **Password hashing** using Bcrypt so that plaintext passwords are never stored.
- **CSRF protection** on all state-changing requests, provided by the framework.
- **SQL-injection prevention** through the use of the Eloquent ORM and parameterised queries.
- **Role-based access control** enforced by authentication and role middleware.
- **Participant-only session rooms** — one-to-one and group meeting rooms verify that the requesting user is a participant before granting entry, and room identifiers are randomly generated.
- **Input validation** on every form to reject malformed or malicious data.
- **Authorisation policies** ensuring that users can act only on resources they own or are permitted to access.
- **Assessment integrity** measures, including tab-switch detection during certification assessments to discourage malpractice.
- **Accidental-logout protection** through a confirmation dialog on the sign-out action, while the logout itself remains a CSRF-protected POST request.

## 4.9 Summary of the Chapter

This chapter has documented the implementation of the PAAUMENTOR platform, including the development tools chosen, the database design realised in MySQL, and the implementation of each functional module — from authentication through mentorship, learning, in-app sessions and group sessions, certification, community growth and administration. It also described the testing strategy and presented test cases whose successful outcomes confirm that the system meets its functional requirements. The next chapter summarises the entire project, draws conclusions and offers recommendations.

---

# CHAPTER FIVE

# SUMMARY, CONCLUSION AND RECOMMENDATIONS

## 5.1 Introduction

This concluding chapter provides a summary of the entire project, presents the conclusions drawn from the work, highlights the contributions and limitations of the system, and offers recommendations together with suggestions for future enhancement.

## 5.2 Summary

The project set out to design and implement a peer mentorship platform for Prince Abubakar Audu University (PAAU), Anyigba, in response to the difficulty students face in obtaining structured academic and career guidance from senior peers and alumni.

The work proceeded through the conventional stages of software development. The first chapter introduced the problem, aim and objectives. The second reviewed related literature and existing mentorship and e-learning systems. The third presented the analysis of the existing informal mentorship arrangements and the design of the proposed system, including its architecture, use cases, data flow and database design. The fourth chapter described the implementation of the system using the Laravel framework and MySQL, its integration with the Google Gemini AI model and the Jitsi Meet conferencing service, and the testing carried out to validate it.

The resulting system, **PAAUMENTOR**, is a comprehensive web application that connects mentees with verified mentors and alumni. It provides AI-powered mentor matching, AI-assisted creation of structured learning paths with task submission and grading, a multi-stage verifiable certificate pipeline complete with QR-code authentication, mentorship session scheduling with in-app video and voice meeting rooms, multi-participant group sessions, one-to-one and group messaging, a skill-exchange marketplace, hackathon management, an AI study assistant and a public AI help widget, and comprehensive administration and verification facilities. Mentors are classified into earned performance tiers (Junior, Senior and Lead) based on the number of mentees they have certified, and the most accomplished are featured on a public, data-driven landing page that builds credibility with prospective users. The platform supports five user roles and is fully responsive with dark-mode support.

## 5.3 Conclusion

The project achieved its aim of developing a functional, AI-augmented peer mentorship platform tailored to the needs of a Nigerian university. The system demonstrates that:

1. Structured, trackable mentorship can be delivered effectively through a web platform, replacing ad-hoc, untracked informal arrangements.
2. Artificial intelligence can be applied practically and affordably to enhance education — in matching mentors to mentees, generating learning curricula, assessing competencies and assisting study.
3. A combination of automated assessment and human verification can produce digital certificates that are both meaningful and independently verifiable.
4. A mentorship community can be made self-sustaining by providing a structured pathway for mentees to become mentors.
5. Rich, real-time mentoring interaction — including video and voice meetings for individuals and groups — can be delivered inside a low-cost web platform by integrating free, open conferencing infrastructure.

In all, the objectives of the study were met, and the completed system is a viable solution to the problem identified at the outset.

## 5.4 Contributions of the Study

The study makes the following contributions:

1. It provides PAAU with a ready-to-deploy mentorship platform that can improve student retention, academic performance and career readiness.
2. It demonstrates a practical, low-cost model for integrating generative AI into an educational web application within the resource constraints typical of Nigerian institutions.
3. It contributes a verifiable-certificate design that combines AI assessment, human reflection and independent verification with public QR-based authentication.
4. It introduces an earned, data-driven mentor reputation model (performance tiers based on actual certified-mentee outcomes) that is surfaced as social proof on a public landing page — an approach that rewards genuine mentoring impact rather than self-promotion.
5. It serves as a reference implementation and learning resource for future students undertaking similar full-stack, AI-integrated projects.

## 5.5 Limitations of the Study

Despite its achievements, the system has the following limitations:

1. **Dependence on internet connectivity and external services** — the AI features require an active internet connection and a valid Gemini API key, and the video/voice session rooms depend on the availability of the public Jitsi Meet service; neither is available offline.
2. **Polling-based messaging** — real-time communication in chat is approximated through periodic polling rather than true WebSocket push, which is less efficient at very large scale.
3. **No native mobile application** — the platform is a responsive web application; a dedicated mobile app was outside the project scope.
4. **Conferencing media is hosted by a third party** — although sessions are conducted inside the application, the audio/video streams themselves are carried by the embedded Jitsi Meet service rather than by university-hosted infrastructure.
5. **Scope of evaluation** — the system was tested principally with demonstration data and a limited set of users rather than in a full institution-wide deployment.

## 5.6 Recommendations

In the light of the work carried out, the following recommendations are made:

1. The university should consider deploying PAAUMENTOR on a production server with a proper domain name and SSL certificate so that students can access it institution-wide.
2. The platform should be officially integrated into the university's student-support framework, with verified staff and alumni encouraged to register as mentors.
3. A reliable internet connection and a funded AI API account should be provisioned to keep the intelligent features continuously available; for heavier usage, a self-hosted Jitsi server should be considered to keep conferencing under institutional control.
4. Periodic data backups and routine security reviews should be instituted to safeguard user data.
5. Users should be given brief orientation on the platform to maximise adoption and correct use.

## 5.7 Suggestions for Future Work

The following enhancements are recommended for future development:

1. **True real-time messaging** using WebSockets (e.g. Laravel Reverb or Pusher) to replace polling for chat, typing indicators and call alerts.
2. **Self-hosted conferencing** — deploying a university-controlled Jitsi (or equivalent WebRTC) server for session rooms, with recording and attendance analytics.
3. **A dedicated mobile application** (Android/iOS) or a Progressive Web App for an improved mobile experience and offline-capable features.
4. **An offline or local AI fallback** so that core intelligent features remain partially available without internet access.
5. **Learning analytics and recommendation dashboards** that give mentors and administrators deeper, data-driven insight into student progress.
6. **Gamification** — badges, points and leaderboards beyond hackathons — to further motivate sustained engagement.
7. **Integration with the university's existing student information system** for automatic enrolment and single sign-on.
8. **Expanded automated test coverage** and a continuous-integration pipeline to support long-term maintenance.

## 5.8 Summary of the Chapter

This chapter summarised the entire project, drew conclusions from the work, stated its contributions and limitations, and offered recommendations and directions for future development. The PAAUMENTOR platform stands as a successful demonstration of how modern web, conferencing and AI technologies can be combined to deliver structured, verifiable and self-sustaining peer mentorship within a Nigerian university.

---

*End of Chapters Three, Four and Five.*
