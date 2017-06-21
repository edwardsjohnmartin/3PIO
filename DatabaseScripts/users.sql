CREATE OR REPLACE FUNCTION sproc_read_user_get_pairs()
RETURNS SETOF key_value_pair AS $$
	SELECT u.id, u.name FROM users AS u WHERE NOT u.is_deleted
	ORDER BY u.id;
$$ LANGUAGE SQL SECURITY DEFINER;

