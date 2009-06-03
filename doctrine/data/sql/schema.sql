CREATE TABLE session (id VARCHAR(32), modified INT, lifetime INT, data TEXT, PRIMARY KEY(id));
CREATE TABLE test (id BIGSERIAL, testint INT, teststring VARCHAR(50), PRIMARY KEY(id));
