--important! these assume only one starter code file, but the database is designed so that more starter code files can be used later

--i'm assuming one starter code file. if there is any number of files other than one, things break.

--i should limit my inner joins...

CREATE OR REPLACE FUNCTION sproc_read_project_count()
RETURNS bigint AS $$
	--speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM projects AS p WHERE NOT p.is_deleted; --i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_project_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text, description text, starter_code text, language json, max_grade double precision) AS $$
	SELECT p.id, p.name, p.description, pscf.contents AS starter_code, row_to_json(ROW(l.id, l.name)::key_value_pair) AS language, p.max_grade
	FROM projects p
	INNER JOIN languages l ON (p.language_id = l.id)
	INNER JOIN project_starter_code_files pscf ON (p.id = pscf.project_id)
	WHERE NOT p.is_deleted
	ORDER BY p.id
	LIMIT sproc_read_project_get_subset.lim OFFSET sproc_read_project_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

--i don't think i will ever want to use this! i probably shouldn't allow it.
CREATE OR REPLACE FUNCTION sproc_read_project_get_all()
RETURNS TABLE(id int, name text, description text, starter_code text, language json, max_grade double precision) AS $$
	SELECT p.id, p.name, p.description, pscf.contents AS starter_code, row_to_json(ROW(l.id, l.name)::key_value_pair) AS language, p.max_grade
	FROM projects p
	INNER JOIN languages l ON (p.language_id = l.id)
	INNER JOIN project_starter_code_files pscf ON (p.id = pscf.project_id)
	WHERE NOT p.is_deleted
	ORDER BY p.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_project_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT p.id, p.name FROM projects AS p WHERE NOT p.is_deleted
	ORDER BY p.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_project_get(id int)
RETURNS TABLE(id int, name text, description text, starter_code text, language json, max_grade double precision) AS $$
	SELECT p.id, p.name, p.description, pscf.contents AS starter_code, row_to_json(ROW(l.id, l.name)::key_value_pair) AS language, p.max_grade
	FROM projects p
	INNER JOIN languages l ON (p.language_id = l.id)
	INNER JOIN project_starter_code_files pscf ON (p.id = pscf.project_id)
	WHERE p.id = sproc_read_project_get.id AND NOT p.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_project_create(name text, description text, starter_code text, language int, max_grade double precision)
RETURNS TABLE(id int) AS $$
	WITH ROWS AS(
	INSERT INTO projects AS p (name, description, language_id, max_grade)
	VALUES (sproc_write_project_create.name, sproc_write_project_create.description, sproc_write_project_create.language, sproc_write_project_create.max_grade) RETURNING p.id
	)
	INSERT INTO project_starter_code_files AS pscf (project_id, contents)
	SELECT ROWS.id, sproc_write_project_create.starter_code FROM ROWS returning pscf.project_id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_project_update(id int, name text, description text, starter_code text, language int, max_grade double precision)
RETURNS VOID AS $$
UPDATE projects AS p
	SET name = sproc_write_project_update.name,
	description = sproc_write_project_update.description,
	language_id = sproc_write_project_update.language,
	max_grade = sproc_write_project_update.max_grade
	WHERE p.id = sproc_write_project_update.id;
UPDATE project_starter_code_files AS pscf
	SET contents = sproc_write_project_update.starter_code
	WHERE pscf.project_id = sproc_write_project_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_project_delete(id int) --should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
	UPDATE projects AS p
	SET is_deleted = TRUE --???
	WHERE p.id = sproc_write_project_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
