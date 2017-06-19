--------the read ones should also return the users in the section!!!!!!

CREATE OR REPLACE FUNCTION sproc_read_concept_count()
RETURNS bigint AS $$
	--speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM concepts AS c WHERE NOT c.is_deleted; --i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_concept_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text, section json, project json, project_open_date timestamp, project_due_date timestamp) AS $$
	SELECT c.id, c.name, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, row_to_json(ROW(p.id, p.name)::key_value_pair) AS project, c.project_open_date, c.project_due_date
	FROM concepts c
	INNER JOIN sections s ON (c.section_id = s.id)
	INNER JOIN projects p ON (c.project_id = p.id)
	WHERE NOT c.is_deleted
	ORDER BY c.project_open_date
	LIMIT sproc_read_concept_get_subset.lim OFFSET sproc_read_concept_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

--i don't think i will ever want to use this! i probably shouldn't allow it.
CREATE OR REPLACE FUNCTION sproc_read_concept_get_all()
RETURNS TABLE(id int, name text, section json, project json, project_open_date timestamp, project_due_date timestamp) AS $$
	SELECT c.id, c.name, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, row_to_json(ROW(p.id, p.name)::key_value_pair) AS project, c.project_open_date, c.project_due_date
	FROM concepts c
	INNER JOIN sections s ON (c.section_id = s.id)
	INNER JOIN projects p ON (c.project_id = p.id)
	WHERE NOT c.is_deleted
	ORDER BY c.project_open_date;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_concept_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT c.id, c.name FROM concepts AS c WHERE NOT c.is_deleted
	ORDER BY c.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_concept_get(id int)
RETURNS TABLE(id int, name text, section json, project json, project_open_date timestamp, project_due_date timestamp) AS $$
	SELECT c.id, c.name, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, row_to_json(ROW(p.id, p.name)::key_value_pair) AS project, c.project_open_date, c.project_due_date
	FROM concepts c
	INNER JOIN sections s ON (c.section_id = s.id)
	INNER JOIN projects p ON (c.project_id = p.id)
	WHERE c.id = sproc_read_concept_get.id AND NOT c.is_deleted;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_concept_create(name text, section int, project int, project_open_date timestamp, project_due_date timestamp)
RETURNS TABLE(id int) AS $$
	INSERT INTO concepts AS c (name, section_id, project_id, project_open_date, project_due_date)
	VALUES (sproc_write_concept_create.name, sproc_write_concept_create.section, sproc_write_concept_create.project, sproc_write_concept_create.project_open_date, sproc_write_concept_create.project_due_date) RETURNING c.id
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_concept_update(id int, name text, section int, project int, project_open_date timestamp, project_due_date timestamp)
RETURNS VOID AS $$
UPDATE concepts AS c
	SET name = sproc_write_concept_update.name,
	section_id = sproc_write_concept_update.section,
	project_id = sproc_write_concept_update.project,
	project_open_date = sproc_write_concept_update.project_open_date,
	project_due_date = sproc_write_concept_update.project_due_date
	WHERE c.id = sproc_write_concept_update.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_concept_delete(id int) --should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
	UPDATE concepts AS c
	SET is_deleted = TRUE --???
	WHERE c.id = sproc_write_concept_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
