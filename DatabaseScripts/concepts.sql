--------the read ones should also return the users in the section!!!!!!

CREATE OR REPLACE FUNCTION sproc_read_concept_count()
RETURNS bigint AS $$
	--speed said to not be a problem in 9.2+
	SELECT COUNT(*) FROM concepts AS c WHERE NOT c.is_deleted; --i should probably check deleted in count... slow?
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_concept_get_subset(lim bigint, off bigint)
RETURNS TABLE(id int, name text, section json, open_date timestamp, project json, project_open_date timestamp, project_due_date timestamp, lessons json) AS $$
	SELECT c.id, c.name, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, c.open_date, row_to_json(ROW(p.id, p.name)::key_value_pair) AS project, c.project_open_date, c.project_due_date, array_to_json(array_agg(ROW(l.id, l.name)::key_value_pair ORDER BY lesson_number)) AS lessons
	FROM concepts c
	INNER JOIN sections s ON (c.section_id = s.id)
	INNER JOIN projects p ON (c.project_id = p.id)
	LEFT JOIN lessons_to_concepts AS ltc ON c.id = ltc.concept_id
	LEFT JOIN lessons AS l on ltc.lesson_id = l.id
	WHERE NOT c.is_deleted
	GROUP BY c.id, s.id, p.id
	ORDER BY c.open_date, c.project_open_date
	LIMIT sproc_read_concept_get_subset.lim OFFSET sproc_read_concept_get_subset.off;
$$ LANGUAGE SQL SECURITY DEFINER;

--i don't think i will ever want to use this! i probably shouldn't allow it.
CREATE OR REPLACE FUNCTION sproc_read_concept_get_all()
RETURNS TABLE(id int, name text, section json, open_date timestamp, project json, project_open_date timestamp, project_due_date timestamp, lessons json) AS $$
	SELECT c.id, c.name, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section,  c.open_date, row_to_json(ROW(p.id, p.name)::key_value_pair) AS project, c.project_open_date, c.project_due_date, array_to_json(array_agg(ROW(l.id, l.name)::key_value_pair ORDER BY lesson_number)) AS lessons
	FROM concepts c
	INNER JOIN sections s ON (c.section_id = s.id)
	INNER JOIN projects p ON (c.project_id = p.id)
	LEFT JOIN lessons_to_concepts AS ltc ON c.id = ltc.concept_id
	LEFT JOIN lessons AS l on ltc.lesson_id = l.id
	WHERE NOT c.is_deleted
	GROUP BY c.id, s.id, p.id
	ORDER BY c.open_date, c.project_open_date;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_concept_get_all_for_section(section_id int)
RETURNS TABLE(id int, name text, section json, open_date timestamp, project json, project_open_date timestamp, project_due_date timestamp, lessons json) AS $$
	SELECT c.id, c.name, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, c.open_date, row_to_json(ROW(p.id, p.name)::key_value_pair) AS project, c.project_open_date, c.project_due_date, array_to_json(array_agg(ROW(l.id, l.name)::key_value_pair ORDER BY ltc.lesson_number)) AS lessons
	FROM concepts c
	INNER JOIN sections s ON (c.section_id = s.id)
	INNER JOIN projects p ON (c.project_id = p.id)
	LEFT JOIN lessons_to_concepts AS ltc ON c.id = ltc.concept_id
	LEFT JOIN lessons AS l on ltc.lesson_id = l.id
	WHERE NOT c.is_deleted AND c.section_id = sproc_read_concept_get_all_for_section.section_id
	GROUP BY c.id, s.id, p.id
	ORDER BY c.open_date, c.project_open_date;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_concept_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT c.id, c.name FROM concepts AS c WHERE NOT c.is_deleted
	ORDER BY c.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_concept_get_pairs_for_owner(owner_id int)
RETURNS SETOF key_value_pair AS $$
	SELECT c.id, c.name FROM concepts AS c
	JOIN sections AS s ON s.id = c.section_id
	WHERE NOT c.is_deleted AND s.teacher_id = sproc_read_concept_get_pairs_for_owner.owner_id
	ORDER BY c.id;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_read_concept_get(id int)
RETURNS TABLE(id int, name text, section json, open_date timestamp, project json, project_open_date timestamp, project_due_date timestamp, lessons json) AS $$
	SELECT c.id, c.name, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, c.open_date, row_to_json(ROW(p.id, p.name)::key_value_pair) AS project, c.project_open_date, c.project_due_date, array_to_json(array_agg(ROW(l.id, l.name)::key_value_pair ORDER BY ltc.lesson_number)) AS lessons
	FROM concepts c
	INNER JOIN sections s ON (c.section_id = s.id)
	INNER JOIN projects p ON (c.project_id = p.id)
	LEFT JOIN lessons_to_concepts AS ltc ON c.id = ltc.concept_id
	LEFT JOIN lessons AS l on ltc.lesson_id = l.id
	WHERE c.id = sproc_read_concept_get.id AND NOT c.is_deleted
	GROUP BY c.id, s.id, p.id;
$$ LANGUAGE SQL SECURITY DEFINER;

/*
CREATE OR REPLACE FUNCTION sproc_read_concept_get_for_user(id int, user_id int)
RETURNS TABLE(id int, name text, section json, open_date timestamp, project json, project_open_date timestamp, project_due_date timestamp, lessons json) AS $$
	SELECT c.id, c.name, row_to_json(ROW(s.id, s.name)::key_value_pair) AS section, c.open_date, row_to_json(ROW(p.id, p.name)::key_value_pair) AS project, c.project_open_date, c.project_due_date, array_to_json(array_agg(ROW(l.id, l.name, cs.id) ORDER BY ltc.lesson_number)) AS lessons
	FROM concepts c
	INNER JOIN sections s ON (c.section_id = s.id)
	INNER JOIN projects p ON (c.project_id = p.id)
	LEFT JOIN lessons_to_concepts AS ltc ON c.id = ltc.concept_id
	LEFT JOIN lessons AS l on ltc.lesson_id = l.id
	LEFT JOIN (SELECT l.id AS lesson_id, MAX(COALESCE(cs.importance, (SELECT MAX(importance) FROM completion_status))) AS importance FROM concepts AS c
	JOIN lessons_to_concepts AS ltc on ltc.concept_id = c.id
	JOIN lessons AS l ON l.id = ltc.lesson_id
	JOIN exercises_to_lessons AS etl ON l.id = etl.lesson_id
	JOIN exercises AS e ON etl.exercise_id = e.id
	JOIN completion_status_to_exercise AS cste ON cste.date_updated = (SELECT MAX(date_updated) FROM completion_status_to_exercise WHERE exercise_id = e.id AND user_id = sproc_read_concept_get_for_user.user_id AND concept_id = c.id AND lesson_id = l.id)
	JOIN completion_status AS cs ON cs.id = cste.completion_status_id
	GROUP BY l.id, ltc.lesson_number) AS inner_query ON l.id = inner_query.lesson_id
	LEFT JOIN completion_status AS cs ON cs.importance = inner_query.importance
	WHERE c.id = sproc_read_concept_get_for_user.id AND NOT c.is_deleted
	GROUP BY c.id, s.id, p.id;
$$ LANGUAGE SQL SECURITY DEFINER;
*/

CREATE OR REPLACE FUNCTION sproc_write_concept_create(name text, section int, open_date timestamp, project int, project_open_date timestamp, project_due_date timestamp, lessons int[])
RETURNS TABLE(id int) AS $$
	DECLARE
		ret_id int;
	BEGIN
	INSERT INTO concepts AS c (name, section_id, open_date, project_id, project_open_date, project_due_date)
	VALUES (sproc_write_concept_create.name, sproc_write_concept_create.section, sproc_write_concept_create.open_date, sproc_write_concept_create.project, sproc_write_concept_create.project_open_date, sproc_write_concept_create.project_due_date) RETURNING c.id INTO ret_id;
	INSERT INTO lessons_to_concepts AS ltc (concept_id, lesson_id, lesson_number) SELECT ret_id, * FROM unnest(lessons) WITH ORDINALITY;
	RETURN QUERY SELECT ret_id AS id;
	END
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_concept_update(id int, name text, section int, open_date timestamp, project int, project_open_date timestamp, project_due_date timestamp, lessons int[])
RETURNS VOID AS $$
UPDATE concepts AS c
	SET name = sproc_write_concept_update.name,
	section_id = sproc_write_concept_update.section,
	open_date = sproc_write_concept_update.open_date,
	project_id = sproc_write_concept_update.project,
	project_open_date = sproc_write_concept_update.project_open_date,
	project_due_date = sproc_write_concept_update.project_due_date
	WHERE c.id = sproc_write_concept_update.id;
DELETE FROM lessons_to_concepts AS ltc WHERE sproc_write_concept_update.id = ltc.concept_id;
INSERT INTO lessons_to_concepts AS ltc (concept_id, lesson_id, lesson_number) SELECT sproc_write_concept_update.id, * FROM unnest(lessons) WITH ORDINALITY;
$$ LANGUAGE SQL SECURITY DEFINER;

CREATE OR REPLACE FUNCTION sproc_write_concept_delete(id int) --should this be delete/undelete, pass in boolean?
RETURNS VOID AS $$
	UPDATE concepts AS c
	SET is_deleted = TRUE --???
	WHERE c.id = sproc_write_concept_delete.id;
$$ LANGUAGE SQL SECURITY DEFINER;
