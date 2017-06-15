CREATE TABLE roles (
	id serial PRIMARY KEY,
	name text
);

CREATE TABLE users (
	id serial PRIMARY KEY,
	email text,
	name text,
	hash character[64], --32 bytes for sha-256
	salt text, --?
	role_id integer REFERENCES roles
);

CREATE TABLE participation_types (
	id serial PRIMARY KEY,
	name text
);

CREATE TABLE languages (
	id serial PRIMARY KEY,
	name text
);

CREATE TABLE tags (
	id serial PRIMARY KEY,
	name text
);

CREATE TABLE exercises (
	id serial PRIMARY KEY,
	name text,
	description text,
	test_code text,
	--hash uuid, --probably won't use this
	language_id integer REFERENCES languages,
	make_file text
);

CREATE TABLE exercise_starter_code_files(
	id serial PRIMARY KEY,
	exercise_id integer REFERENCES exercises,
	file_name text,
	file_contents text
);

CREATE TABLE projects (
	id serial PRIMARY KEY,
	name text,
	description text,
	--hash uuid,
	language_id integer REFERENCES languages,
	make_file text,
	max_grade double precision
);

CREATE TABLE project_starter_code_files(
	file_id serial PRIMARY KEY,
	project_id integer REFERENCES projects,
	file_name text,
	file_contents text
);

CREATE TABLE lessons (
	id serial PRIMARY KEY,
	name text,
	owner_id integer REFERENCES users
);

CREATE TABLE completion_status (
	id serial PRIMARY KEY,
	name text
);

CREATE TABLE courses (
	id serial PRIMARY KEY,
	name text
);

CREATE TABLE sections (
	id serial PRIMARY KEY,
	name text,
	course_id integer REFERENCES courses,
	teacher_id integer REFERENCES users,
	start_date timestamp,
	end_date timestamp
);

--modules
CREATE TABLE concepts ( --order by date? have an order?
	id serial PRIMARY KEY,
	name text,
	section_id integer REFERENCES sections,
	project_id integer REFERENCES projects,
	project_open_date timestamp,
	project_due_date timestamp
);

CREATE TABLE tags_to_exercises (
	tag_id integer REFERENCES tags,
	exercise_id integer REFERENCES exercises
);

CREATE TABLE exercises_to_lessons (
	exercise_id integer REFERENCES exercises,
	lesson_id integer REFERENCES lessons,
	exercise_number integer
);

CREATE TABLE lessons_to_concepts (
	lesson_id integer REFERENCES lessons,
	concept_id integer REFERENCES concepts,
	lesson_number integer
);

CREATE TABLE project_teams (
	id serial PRIMARY KEY,
	concept_id integer REFERENCES concepts,
	user_id integer REFERENCES users
);

--where to put the code?
CREATE TABLE project_code_files ( --i need multiple files
	team_id integer REFERENCES project_teams,
	id serial PRIMARY KEY,
	file_name text,
	file_content text
);

CREATE TABLE completion_status_to_exercise (
	exercise_id integer REFERENCES exercises,
	lesson_id integer REFERENCES lessons,
	concept_id integer REFERENCES concepts,
	--now, should i have the section id here, too? it's not really necessary, but would it be convenient?
	date_updated timestamp,
	completion_status_id integer REFERENCES completion_status,
	user_id integer REFERENCES users
);

CREATE TABLE completion_status_to_project (
	project_id integer REFERENCES projects,
	concept_id integer REFERENCES concepts,
	date_updated timestamp,
	completion_status_id integer REFERENCES completion_status,
	team_id integer REFERENCES project_teams
);

CREATE TABLE project_grades ( --might need a better name
	project_id integer REFERENCES projects, --since each concept has only one project, i don't need this
	concept_id integer REFERENCES concepts,
	team_id integer REFERENCES project_teams, --are there any other ids i need?
	grade double precision
);

CREATE TABLE users_to_section (
	user_id integer REFERENCES users,
	section_id integer REFERENCES sections,
	participation_type_id integer REFERENCES participation_types --not teacher. grader or student.
);

CREATE TABLE functions (
	id serial PRIMARY KEY,
	section_id integer REFERENCES sections,
	user_id integer REFERENCES users,
	name text,
	code text,
	UNIQUE(section_id, user_id, name)
);
