<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>My Courses - GRADELY</title>
    <style>
        :root {
            --color-primary: #1976D2;
            --color-secondary: #00897B;
            --bg: #f4f7f6;
            --muted: #666;
            --white: #fff;
            --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: var(--font);
            background: var(--bg);
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--color-secondary);
            color: var(--white);
            padding: 20px;
            box-shadow: 2px 0 6px rgba(0,0,0,0.1);
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
        }
        .sidebar a {
            display: block;
            color: var(--white);
            text-decoration: none;
            padding: 10px 12px;
            margin: 8px 0;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
        }
        .sidebar .logout {
            background: rgba(255,0,0,0.3);
            margin-top: 30px;
        }
        .sidebar .logout:hover {
            background: rgba(255,0,0,0.5);
        }
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            background: var(--white);
        }
        .page-header {
            margin-bottom: 30px;
        }
        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
        }
        .page-subtitle {
            font-size: 16px;
            color: var(--muted);
            font-weight: 400;
        }
        /* Filters and Controls */
        .controls {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            align-items: center;
        }
        .dropdown, .search-input {
            padding: 10px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background: var(--white);
            font-size: 14px;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .dropdown:hover, .search-input:hover {
            border-color: #b0b0b0;
        }
        .dropdown:focus, .search-input:focus {
            outline: none;
            border-color: var(--color-primary);
        }
        .search-input {
            flex: 1;
            min-width: 200px;
            cursor: text;
        }
        .dropdown {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 150px;
        }
        /* Course Cards */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }
        .course-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            position: relative;
            height: 200px;
            display: flex;
            flex-direction: column;
        }
        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .course-card-header {
            height: 120px;
            position: relative;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .course-card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.1);
        }
        .course-code {
            position: relative;
            z-index: 1;
            font-size: 24px;
            font-weight: 700;
            color: var(--white);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .course-card-body {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .course-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 4px;
            line-height: 1.4;
        }
        .course-faculty {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .course-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .course-progress {
            font-size: 13px;
            color: var(--muted);
        }
        .course-menu {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 4px;
            padding: 6px 8px;
            cursor: pointer;
            font-size: 18px;
            color: #666;
            transition: background 0.2s;
        }
        .course-menu:hover {
            background: rgba(255,255,255,1);
        }
        /* Pattern backgrounds */
        .pattern-purple {
            background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px),
                repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);
        }
        .pattern-blue {
            background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px),
                repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);
        }
        .pattern-teal {
            background: linear-gradient(135deg, #00897B 0%, #00695C 100%);
            background-image: 
                radial-gradient(circle at 20px 20px, rgba(255,255,255,0.1) 2px, transparent 0),
                radial-gradient(circle at 60px 60px, rgba(255,255,255,0.1) 2px, transparent 0);
            background-size: 40px 40px;
        }
        .pattern-green {
            background: linear-gradient(135deg, #43A047 0%, #2E7D32 100%);
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);
        }
        .pattern-orange {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
            background-image: 
                radial-gradient(circle at 20px 20px, rgba(255,255,255,0.1) 2px, transparent 0);
            background-size: 40px 40px;
        }
        .pattern-red {
            background: linear-gradient(135deg, #E53935 0%, #C62828 100%);
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .main-content {
                padding: 20px;
            }
            .courses-grid {
                grid-template-columns: 1fr;
            }
            .controls {
                flex-direction: column;
            }
            .search-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>GRADELY</h2>
            <a href="{{ route('lecturer.courses') }}" class="active">📚 My Courses</a>
            <a href="#students">👥 Students</a>
            <a href="#grades">📊 Grade Management</a>
            <a href="#assignments">✏️ Assignments</a>
            <a href="#analytics">📈 Analytics</a>
            <a href="{{ route('profile.view') }}">👤 Profile</a>
            <a href="{{ route('lecturer.dashboard') }}">🏠 Dashboard</a>
            <a href="{{ url('/logout') }}" class="logout">🚪 Logout</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">My courses</h1>
                <p class="page-subtitle">Course overview</p>
            </div>

            <!-- Controls -->
            <div class="controls">
                <select class="dropdown" id="filterDropdown">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                </select>
                <input type="text" class="search-input" id="searchInput" placeholder="Search">
                <select class="dropdown" id="sortDropdown">
                    <option value="name">Sort by short name</option>
                    <option value="code">Sort by code</option>
                    <option value="recent">Most recent</option>
                </select>
                <select class="dropdown" id="viewDropdown">
                    <option value="card">Card</option>
                    <option value="list">List</option>
                </select>
            </div>

            <!-- Course Cards -->
            <div class="courses-grid" id="coursesGrid">
                @forelse($courses as $index => $course)
                    @php
                        $patterns = ['pattern-purple', 'pattern-blue', 'pattern-teal', 'pattern-green', 'pattern-orange', 'pattern-red'];
                        $patternClass = $patterns[$index % count($patterns)];
                        // Calculate progress (simplified - you can enhance this based on assignments/submissions)
                        $totalAssignments = $course->assignments_count ?? 0;
                        $progress = $totalAssignments > 0 ? min(100, ($totalAssignments * 10)) : 0;
                    @endphp
                    <a href="{{ route('lecturer.course.show', $course->id) }}" style="text-decoration: none; color: inherit;">
                        <div class="course-card">
                            <div class="course-card-header {{ $patternClass }}">
                                <div class="course-code">{{ $course->course_code }}</div>
                                <button class="course-menu" onclick="event.preventDefault(); return false;">⋮</button>
                            </div>
                            <div class="course-card-body">
                                <div>
                                    <div class="course-title">{{ strtoupper($course->course_name) }}</div>
                                    <div class="course-faculty">FACULTY OF COMPUTING</div>
                                </div>
                                <div class="course-footer">
                                    <span class="course-progress">{{ $progress }}% complete</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--muted);">
                        <p style="font-size: 18px; margin-bottom: 8px;">No courses found</p>
                        <p style="font-size: 14px;">You haven't been assigned to any courses yet.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>

    <script>
        // Simple search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.course-card');
            
            cards.forEach(card => {
                const title = card.querySelector('.course-title').textContent.toLowerCase();
                const code = card.querySelector('.course-code').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || code.includes(searchTerm)) {
                    card.closest('a').style.display = '';
                } else {
                    card.closest('a').style.display = 'none';
                }
            });
        });

        // Prevent menu button from navigating
        document.querySelectorAll('.course-menu').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Menu functionality can be added here
            });
        });
    </script>
</body>
</html>

