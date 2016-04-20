INSERT INTO public.departments (department_id, name, descr) VALUES ('GW', 'Genowise', 'Genowise');
INSERT INTO public.roles (name, descr) VALUES ('admin', 'Administrator');
INSERT INTO public.roles (name, descr) VALUES ('common_user', 'Common');
INSERT INTO public.users (name, label, department_id, descr) VALUES ('admin', 'admin', 'GW', 'Administrator');
UPDATE public.users SET superuser = TRUE WHERE name = 'admin';
SELECT public.add_user_role(get_user_id('admin'), get_role_id('admin'));
