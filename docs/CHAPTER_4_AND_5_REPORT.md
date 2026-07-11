# PAAUMENTOR — A PEER MENTORSHIP PLATFORM FOR PRINCE ABUBAKAR AUDU UNIVERSITY (PAAU), ANYIGBA

**Project Report — Chapters Four and Five**

**By:** Moses Goddey Joseph (Matric No: 23CS1004)
**Department:** Computer Science
**Institution:** Prince Abubakar Audu University, Anyigba, Kogi State, Nigeria
**Supervisor:** Mr. Richard Akomodi

---

# CHAPTER FOUR

# SYSTEM IMPLEMENTATION, TESTING AND RESULTS

## 4.1 Introduction

This chapter presents the actual implementation of the PAAUMENTOR peer mentorship platform whose analysis and design were discussed in the preceding chapters. It describes the software and hardware tools used in building the system, the implementation of the database, the development of the individual functional modules, and the manner in which the various subsystems were integrated into a single working web application.

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
| Artificial Intelligence | Google Gemini 2.5 Flash (REST API) | Mentor matching, learning-path generation, study assistant, assessment generation |
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
| Network | Internet connection (required for AI features) |

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

The database, named `paaumentor`, was implemented in MySQL and created entirely through Laravel migration files, which guarantee that the schema can be reproduced identically on any installation. The database comprises over thirty interrelated tables that store users, mentorship relationships, learning content, communications, certificates, assessments and platform activities. Table 4.4 describes the principal tables in the system.

**Table 4.4: Principal Database Tables**

| Table | Purpose |
|---|---|
| `users` | Stores all user accounts (mentees, mentors, alumni, admins, verifiers) with name, email, student ID, hashed password, role, department, level, bio and verification status |
| `skills` | Catalogue of skills, linked to users to express competencies |
| `mentorships` | Records mentor–mentee relationships and their status (pending, accepted, rejected) |
| `sessions` | Mentorship sessions (video, voice, chat) with scheduling, status and call outcome |
| `learning_paths` | Structured learning programmes created by mentors |
| `learning_modules` | Modules belonging to a learning path |
| `learning_tasks` | Individual assessable tasks within a module |
| `task_submissions` | Mentee submissions to tasks, with mentor grades and feedback |
| `conversations` / `messages` | One-to-one messaging between users |
| `study_groups`, `study_group_members`, `study_group_messages` | Group study spaces and group chat |
| `shared_resources` / `resources` | Shared learning materials library |
| `skill_exchanges`, `skill_exchange_requests` | Peer-to-peer skill exchange marketplace |
| `certificates` | Issued certificates with unique IDs and QR verification data |
| `certificate_requests` | Pipeline records for certificate issuance, including mentor reflections |
| `assessments`, `assessment_questions`, `assessment_attempts` | AI-generated certification assessments and attempts |
| `mentor_upgrade_requests` | Mentee applications to be upgraded to mentor |
| `upgrade_assessments` (+ questions, attempts) | AI-generated assessments for the mentor-upgrade pipeline |
| `hackathons`, `hackathon_teams`, `hackathon_submissions`, `hackathon_scores` | Hackathon events, teams, project submissions and judging |
| `ratings` | Ratings and reviews of mentors |
| `notifications` | In-app notification records |

### 4.4.1 Sample Table Structure — `users`

The `users` table is central to the system because every action is associated with a user account. Its structure is shown in Table 4.5.

**Table 4.5: Structure of the `users` Table**

| Field | Data Type | Constraint / Description |
|---|---|---|
| id | BIGINT | Primary key, auto-increment |
| first_name | VARCHAR | Not null |
| last_name | VARCHAR | Not null |
| email | VARCHAR | Unique, not null |
| student_id | VARCHAR | Unique, nullable (used as alternative login) |
| password | VARCHAR | Bcrypt-hashed, not null |
| role | ENUM | `mentee`, `mentor`, `alumni`, `admin` (default `mentee`) |
| department | VARCHAR | Nullable |
| level | VARCHAR | Nullable (e.g. 100–500 level) |
| bio | TEXT | Nullable |
| phone | VARCHAR | Nullable |
| avatar | VARCHAR | Nullable (profile-photo path) |
| is_verified | BOOLEAN | Default false |
| is_active | BOOLEAN | Default true (used for suspension) |
| availability | VARCHAR | Nullable |
| created_at / updated_at | TIMESTAMP | Record timestamps |

> **Figure 4.1:** Entity-Relationship Diagram of the PAAUMENTOR database *(insert ERD here)*

## 4.5 System Implementation (Module Implementation)

The functionality of PAAUMENTOR was implemented as a collection of cooperating modules, each handled by a dedicated controller and supported by one or more Eloquent models and Blade views. The major modules are described below.

### 4.5.1 Authentication and User Management Module

This module governs registration, login, logout and password recovery. A distinctive feature is that users may log in either with their **email address** or with their **student ID**, which is convenient for students who readily remember their matriculation numbers. Passwords are securely hashed using Bcrypt, and the "forgot password" workflow issues a tokenised reset link by email. Five user roles are supported — mentee, mentor, alumni, administrator and verifier — and access to protected areas is enforced by authentication and role middleware.

> **Figure 4.2:** Login page *(insert screenshot `docs/screenshots/02-login.png`)*

### 4.5.2 Dashboard Module

After login, each user is presented with a role-specific dashboard summarising relevant information — for a mentee, active mentors, learning progress and upcoming sessions; for a mentor, mentee requests and grading tasks; for an administrator, platform-wide statistics. Progress and statistics are visualised with charts rendered in JavaScript.

### 4.5.3 Mentor Discovery and Matching Module

Mentees can browse a directory of verified mentors and, crucially, use the **AI-powered smart-matching** feature. The mentee enters their goals and interests, and the system sends these together with the profiles of available mentors to the Google Gemini model, which returns a ranked list of the most suitable mentors, each accompanied by a one-sentence justification. Mentees may then send mentorship requests, which mentors accept or decline.

To help mentees judge credibility at a glance, the system also classifies every mentor into a **performance tier** that is computed automatically from the number of mentees they have successfully certified: a mentor with fewer than five certified mentees is a *Junior Mentor*, five or more makes a *Senior Mentor*, and fifteen or more a *Lead Mentor*. The tier is therefore an earned, data-driven badge rather than a self-declared title, and it is used both to rank mentors in the directory and to feature the most accomplished mentors on the public landing page (Section 4.5.14).

### 4.5.4 Learning Path Module

Mentors create structured learning paths consisting of modules and assessable tasks. To accelerate this, the system offers **AI-assisted generation**: given a topic, learner level and duration in weeks, Gemini returns a complete, structured curriculum (3–5 modules, each with 2–4 practical tasks) as JSON, which the mentor can review and save. Mentees follow the path, submit work for each task, and mentors grade the submissions and provide feedback.

### 4.5.5 Session Management Module

This module allows mentors and mentees to schedule and conduct mentorship sessions in video, voice or chat form. Sessions carry a status (scheduled, completed, cancelled) and a call outcome (answered, missed), enabling accurate tracking of mentoring activity, which in turn feeds the mentor-upgrade eligibility criteria.

### 4.5.6 Messaging and Study Groups Module

Real-time-style one-to-one messaging is provided between connected users, implemented through periodic client-side polling with typing indicators and read receipts. Study groups extend this to many-to-many collaboration, with a shared group chat for collective learning.

### 4.5.7 Certificate Pipeline Module

This is one of the most significant modules. When a mentee completes every task on a learning path and the mentor grades them, the system invokes Gemini to generate a **30-question assessment**. The mentee must score at least **70 %** to pass. The mentor then writes a reflection on the mentee's performance, after which a **verifier** reviews the request and, on approval, a certificate is issued. Each certificate carries a unique identifier and a **QR code** that links to a public verification page, allowing third parties to confirm authenticity without logging in. The certificate document itself is rendered as a downloadable PDF.

> **Figure 4.3:** Issued certificate with QR code *(insert screenshot `docs/screenshots/05-certificate.png`)*

### 4.5.8 Mentor-Upgrade Module

To grow the pool of mentors organically, an eligible mentee (one who has completed at least five sessions, earned at least one certificate, completed at least one learning path and has an active mentor) may apply to become a mentor. The application triggers an AI-generated upgrade assessment; on passing, the mentee's current mentor writes a recommendation and an administrator gives final approval, after which the user's role is upgraded.

### 4.5.9 Skill Exchange Module

This module implements a peer-to-peer marketplace in which students offer a skill they possess in exchange for a skill they wish to learn. Requests can be sent, accepted and converted into a direct chat, fostering reciprocal, non-hierarchical learning.

### 4.5.10 Hackathon Module

PAAUMENTOR supports the organisation of hackathons: events with team creation and joining, project submission, judge assignment, scoring, leaderboards and result publication. Volunteer coaching by mentors is also supported.

### 4.5.11 AI Study Buddy Module

An always-available AI study assistant, powered by Gemini, answers students' academic questions in a conversational interface, retaining short-term conversation history for context and politely redirecting non-academic queries.

### 4.5.12 Administration and Verification Module

Administrators manage the platform: viewing aggregate statistics (total users, sessions, certificates, top skills), verifying and suspending users, and creating verifier accounts. Verifiers form a specialised role responsible for approving mentor portfolios and reviewing certificate requests, thereby introducing a quality-control checkpoint into the system.

### 4.5.13 Notification Module

A cross-cutting in-app notification system informs users of relevant events — mentorship responses, new messages, grading, certificate issuance, incoming calls, and so on — with unread counts and a "mark all as read" facility.

### 4.5.14 Public Landing Page and Featured-Mentors Module

The platform presents a public landing page (accessible without logging in) that serves as the entry point for prospective users. In addition to the hero banner, platform statistics and a summary of features, the landing page includes a **"Meet Our Mentors"** showcase that displays the platform's most accomplished mentors. This feature was inspired by the profile-presentation style of established commercial mentorship marketplaces, but adapted to the free, university context of PAAUMENTOR.

The showcase is **data-driven**: rather than listing hard-coded names, it queries the database for verified, active mentors, ranks them by their earned performance tier (Lead, then Senior, then Junior) and, within a tier, by their average rating, and displays the top mentors as cards. Each card shows the mentor's photograph (or their initials where no photograph is available), an earned tier badge (for example, *Senior Mentor*), name, role and department, average star rating with the number of reviews, and a sample of their skills. A call-to-action button invites visitors to sign in and connect. Because the section is generated from live data, it remains current automatically as mentors gain experience, and it gracefully hides itself when no verified mentors yet exist. This feature strengthens the platform's credibility to first-time visitors by foregrounding genuine, accomplished mentors as social proof.

> **Figure 4.4:** Landing-page "Meet Our Mentors" showcase *(insert screenshot)*

### 4.5.15 Secure Sign-Out Confirmation

To prevent accidental loss of session and to follow good usability practice, the sign-out action was enhanced with a confirmation step. When a user clicks **Sign Out** from any part of the application, the browser presents a confirmation dialog ("Are you sure you want to sign out?") that must be acknowledged before the session is terminated. This guards against unintentional logouts — for example, when a user clicks the control by mistake — while the actual logout itself remains a secure, CSRF-protected POST request.

## 4.6 System Testing

Testing was carried out to verify that each module behaves according to specification and that the modules work correctly together. A combination of testing approaches was adopted.

### 4.6.1 Testing Approaches

1. **Unit Testing** — Individual functions and methods (for example, validation rules, grade calculation, and JSON extraction from AI responses) were tested in isolation.
2. **Integration Testing** — Interactions between modules were tested, such as the flow from task completion, through assessment generation, to certificate issuance.
3. **System Testing** — The complete application was exercised end-to-end to confirm that all features operate together correctly.
4. **User Acceptance Testing (UAT)** — Representative users (students) interacted with the system to confirm that it met their expectations and was usable.

### 4.6.2 Test Cases and Results

Tables 4.6 presents representative test cases used to validate core functionality, together with the expected and actual outcomes.

**Table 4.6: Selected Test Cases**

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
| TC-15 | Mentor-upgrade eligibility | Mentee not meeting criteria | Application blocked with reason | As expected | Pass |
| TC-16 | Suspend user (admin) | Admin toggles user status | User deactivated, cannot log in | As expected | Pass |
| TC-17 | AI Study Buddy query | Academic question | Relevant educational answer returned | As expected | Pass |
| TC-18 | Form validation | Empty required field | Validation error shown, no save | As expected | Pass |
| TC-19 | Featured mentors on landing page | Visit landing page as guest | Top verified mentors displayed, ranked by tier and rating | As expected | Pass |
| TC-20 | Mentor tier classification | Mentor with ≥ 5 certified mentees | Tier shown as "Senior Mentor" | As expected | Pass |
| TC-21 | Sign-out confirmation | Click "Sign Out", then Cancel | Logout aborted, user remains signed in | As expected | Pass |
| TC-22 | Sign-out confirmation | Click "Sign Out", then OK | Session terminated, redirected to landing page | As expected | Pass |

### 4.6.3 Discussion of Test Results

All representative test cases produced the expected outcomes, indicating that the core functional requirements of the system were correctly implemented. Validation logic successfully rejected malformed input, role-based access control correctly prevented unauthorised access, and the AI-dependent features returned usable results when a valid Gemini API key and internet connection were available. Where the external AI service was momentarily unavailable, the system degraded gracefully — for example, returning an empty result set rather than crashing — which confirmed the robustness of the error-handling logic.

## 4.7 Results and Discussion

The completed PAAUMENTOR system successfully realises all the objectives set out at the beginning of the project. It provides a single, integrated platform on which students of Prince Abubakar Audu University can find mentors, follow structured learning paths, communicate, collaborate in study groups, exchange skills, participate in hackathons, and earn verifiable certificates.

Several outcomes are worth highlighting:

1. **Effective mentor–mentee matching.** The integration of the Gemini AI model produced relevant mentor recommendations from free-text descriptions of mentee goals, demonstrating that artificial intelligence can meaningfully reduce the friction of finding an appropriate mentor.
2. **A rigorous, trustworthy certification pipeline.** By combining automated assessment, human mentor reflection and independent verifier approval, the system issues certificates whose authenticity can be checked publicly through a QR code. This addresses the common problem of unverifiable informal learning.
3. **A self-sustaining mentorship ecosystem.** The mentor-upgrade pathway enables successful mentees to become mentors, allowing the community to grow without continual external intervention.
4. **Responsive, accessible interface.** The interface is fully responsive and includes a dark-mode option, making the platform usable across devices and lighting conditions.
5. **Credible first impression through earned reputation.** The data-driven featured-mentors showcase on the landing page surfaces genuinely accomplished mentors (ranked by an earned performance tier and rating), providing social proof that encourages prospective students to register, without resorting to fabricated or marketing-style claims.

The system therefore demonstrates that a modern, AI-augmented mentorship platform can be built using accessible, low-cost, open-source technologies suited to the Nigerian university context.

## 4.8 System Security

Several measures were implemented to protect the system and its data:

- **Password hashing** using Bcrypt so that plaintext passwords are never stored.
- **CSRF protection** on all state-changing requests, provided by the framework.
- **SQL-injection prevention** through the use of the Eloquent ORM and parameterised queries.
- **Role-based access control** enforced by authentication and role middleware.
- **Input validation** on every form to reject malformed or malicious data.
- **Authorisation policies** ensuring that users can act only on resources they own or are permitted to access.
- **Assessment integrity** measures, including tab-switch detection during certification assessments to discourage malpractice.
- **Accidental-logout protection** through a confirmation dialog on the sign-out action, while the logout itself remains a CSRF-protected POST request.

## 4.9 Summary of the Chapter

This chapter has documented the implementation of the PAAUMENTOR platform, including the development tools chosen, the database design realised in MySQL, and the implementation of each functional module. It also described the testing strategy and presented test cases whose successful outcomes confirm that the system meets its functional requirements. The next chapter summarises the entire project, draws conclusions and offers recommendations.

---

# CHAPTER FIVE

# SUMMARY, CONCLUSION AND RECOMMENDATIONS

## 5.1 Introduction

This concluding chapter provides a summary of the entire project, presents the conclusions drawn from the work, highlights the contributions and limitations of the system, and offers recommendations together with suggestions for future enhancement.

## 5.2 Summary

The project set out to design and implement a peer mentorship platform for Prince Abubakar Audu University (PAAU), Anyigba, in response to the difficulty students face in obtaining structured academic and career guidance from senior peers and alumni.

The work proceeded through the conventional stages of software development. The first chapter introduced the problem, aim and objectives. The second reviewed related literature and existing mentorship and e-learning systems. The third presented the analysis of the existing manual/informal mentorship arrangements and the design of the proposed system, including its architecture, data flow and database design. The fourth chapter, presented above, described the implementation of the system using the Laravel framework and MySQL, its integration with the Google Gemini AI model, and the testing carried out to validate it.

The resulting system, **PAAUMENTOR**, is a comprehensive web application that connects mentees with verified mentors and alumni. It provides AI-powered mentor matching, AI-assisted creation of structured learning paths with task submission and grading, a multi-stage verifiable certificate pipeline complete with QR-code authentication, mentorship session scheduling, one-to-one and group messaging, a skill-exchange marketplace, hackathon management, an AI study assistant, and comprehensive administration and verification facilities. Mentors are classified into earned performance tiers (Junior, Senior and Lead) based on the number of mentees they have certified, and the most accomplished are featured on a public, data-driven landing page that builds credibility with prospective users. The platform supports five user roles and is fully responsive with dark-mode support.

## 5.3 Conclusion

The project achieved its aim of developing a functional, AI-augmented peer mentorship platform tailored to the needs of a Nigerian university. The system demonstrates that:

1. Structured, trackable mentorship can be delivered effectively through a web platform, replacing ad-hoc, untracked informal arrangements.
2. Artificial intelligence can be applied practically and affordably to enhance education — in matching mentors to mentees, generating learning curricula, assessing competencies and assisting study.
3. A combination of automated assessment and human verification can produce digital certificates that are both meaningful and independently verifiable.
4. A mentorship community can be made self-sustaining by providing a structured pathway for mentees to become mentors.

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

1. **Dependence on internet connectivity and an external AI service** — the AI features require an active internet connection and a valid Gemini API key; they are unavailable offline.
2. **Polling-based messaging** — real-time communication is approximated through periodic polling rather than true WebSocket push, which is less efficient at very large scale.
3. **No native mobile application** — the platform is a responsive web application; a dedicated mobile app was outside the project scope.
4. **No integrated in-app video calling** — although session types include video and voice, the platform signals and tracks calls rather than hosting the media stream itself.
5. **Scope of evaluation** — the system was tested principally with demonstration data and a limited set of users rather than in a full institution-wide deployment.

## 5.6 Recommendations

In the light of the work carried out, the following recommendations are made:

1. The university should consider deploying PAAUMENTOR on a production server with a proper domain name and SSL certificate so that students can access it institution-wide.
2. The platform should be officially integrated into the university's student-support framework, with verified staff and alumni encouraged to register as mentors.
3. A reliable internet connection and a funded AI API account should be provisioned to keep the intelligent features continuously available.
4. Periodic data backups and routine security reviews should be instituted to safeguard user data.
5. Users should be given brief orientation on the platform to maximise adoption and correct use.

## 5.7 Suggestions for Future Work

The following enhancements are recommended for future development:

1. **Real-time communication** using WebSockets (e.g. Laravel Reverb or Pusher) to replace polling, and **native in-app video/voice calling** via WebRTC.
2. **A dedicated mobile application** (Android/iOS) or a Progressive Web App for an improved mobile experience and offline-capable features.
3. **An offline or local AI fallback** so that core intelligent features remain partially available without internet access.
4. **Learning analytics and recommendation dashboards** that give mentors and administrators deeper, data-driven insight into student progress.
5. **Gamification** — badges, points and leaderboards beyond hackathons — to further motivate sustained engagement.
6. **Integration with the university's existing student information system** for automatic enrolment and single sign-on.
7. **Expanded automated test coverage** and a continuous-integration pipeline to support long-term maintenance.

## 5.8 Summary of the Chapter

This chapter summarised the entire project, drew conclusions from the work, stated its contributions and limitations, and offered recommendations and directions for future development. The PAAUMENTOR platform stands as a successful demonstration of how modern web and AI technologies can be combined to deliver structured, verifiable and self-sustaining peer mentorship within a Nigerian university.

---

*End of Chapters Four and Five.*
