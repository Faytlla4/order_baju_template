--
-- PostgreSQL database dump
--


-- Dumped from database version 18.4
-- Dumped by pg_dump version 18.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: activities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.activities (
    activity_id bigint NOT NULL,
    user_id bigint NOT NULL,
    activity character varying(255) NOT NULL,
    module character varying(255) NOT NULL,
    created_on timestamp(6) without time zone NOT NULL,
    deleted smallint DEFAULT 0 NOT NULL
);


--
-- Name: activities_activity_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.activities_activity_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: activities_activity_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.activities_activity_id_seq OWNED BY public.activities.activity_id;


--
-- Name: approval_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.approval_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: approval; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.approval (
    id integer DEFAULT nextval('public.approval_id_seq'::regclass) NOT NULL,
    kode_layanan character varying(3) NOT NULL,
    tgl_buat timestamp(6) without time zone DEFAULT now() NOT NULL,
    id_transaksi integer NOT NULL,
    id_pemohon integer,
    status_total smallint DEFAULT 0 NOT NULL,
    status_approval smallint DEFAULT 0 NOT NULL
);


--
-- Name: approval_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.approval_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: approval_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.approval_items (
    id integer DEFAULT nextval('public.approval_items_id_seq'::regclass) NOT NULL,
    role_id integer NOT NULL,
    status_approval integer DEFAULT 0 NOT NULL,
    tgl_proses timestamp(6) without time zone,
    approval_id integer NOT NULL,
    index integer NOT NULL
);


--
-- Name: approval_items_desc_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.approval_items_desc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: approval_items_desc; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.approval_items_desc (
    id integer DEFAULT nextval('public.approval_items_desc_id_seq'::regclass) NOT NULL,
    approval_item_id integer NOT NULL,
    user_id integer NOT NULL,
    description text NOT NULL,
    status_approval smallint NOT NULL,
    tgl_proses timestamp(6) without time zone DEFAULT now() NOT NULL
);


--
-- Name: authority_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.authority_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: authority; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.authority (
    id integer DEFAULT nextval('public.authority_id_seq'::regclass) NOT NULL,
    kode_layanan character varying(255) NOT NULL
);


--
-- Name: authority_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.authority_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: authority_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.authority_items (
    id integer DEFAULT nextval('public.authority_items_id_seq'::regclass) NOT NULL,
    index integer NOT NULL,
    authority_id integer NOT NULL,
    role_id integer NOT NULL
);


--
-- Name: backup_database_history; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.backup_database_history (
    id integer NOT NULL,
    file_name character varying(255) NOT NULL,
    file_path character varying(500) NOT NULL,
    file_size integer DEFAULT 0,
    status character varying(50) DEFAULT 'Berhasil'::character varying,
    created_on timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: backup_database_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.backup_database_history_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: backup_database_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.backup_database_history_id_seq OWNED BY public.backup_database_history.id;


--
-- Name: backup_document_history; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.backup_document_history (
    id integer NOT NULL,
    file_name character varying(255) NOT NULL,
    file_path character varying(500) NOT NULL,
    file_size integer DEFAULT 0,
    jumlah_dokumen integer DEFAULT 0,
    filter_used text,
    created_on timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: backup_document_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.backup_document_history_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: backup_document_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.backup_document_history_id_seq OWNED BY public.backup_document_history.id;


--
-- Name: ci3_sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ci3_sessions (
    id character varying(128) NOT NULL,
    ip_address character varying(45) NOT NULL,
    "timestamp" bigint NOT NULL,
    data bytea NOT NULL
);


--
-- Name: email_queue; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.email_queue (
    id integer NOT NULL,
    to_email character varying(254) NOT NULL,
    subject character varying(255) NOT NULL,
    message text NOT NULL,
    alt_message text,
    max_attempts integer NOT NULL,
    attempts integer NOT NULL,
    success smallint NOT NULL,
    date_published timestamp(6) without time zone,
    last_attempt timestamp(6) without time zone,
    date_sent timestamp(6) without time zone,
    csv_attachment text
);


--
-- Name: email_queue_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.email_queue_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: email_queue_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.email_queue_id_seq OWNED BY public.email_queue.id;


--
-- Name: login_attempts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.login_attempts (
    id bigint NOT NULL,
    ip_address character varying(45) NOT NULL,
    login character varying(255) NOT NULL,
    "time" timestamp(6) without time zone NOT NULL
);


--
-- Name: login_attempts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.login_attempts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: login_attempts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.login_attempts_id_seq OWNED BY public.login_attempts.id;


--
-- Name: master_jenis_baju; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.master_jenis_baju (
    id integer NOT NULL,
    nama_jenis character varying(50) NOT NULL,
    urutan integer DEFAULT 0 NOT NULL,
    keterangan character varying(255),
    status smallint DEFAULT '1'::smallint NOT NULL,
    created_on timestamp without time zone DEFAULT '2026-08-08 22:40:14.781162'::timestamp without time zone NOT NULL
);


--
-- Name: master_jenis_baju_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.master_jenis_baju_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: master_jenis_baju_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.master_jenis_baju_id_seq OWNED BY public.master_jenis_baju.id;


--
-- Name: master_ukuran; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.master_ukuran (
    id integer NOT NULL,
    nama_ukuran character varying(20) NOT NULL,
    urutan integer DEFAULT 0 NOT NULL,
    status smallint DEFAULT '1'::smallint NOT NULL,
    created_on timestamp without time zone DEFAULT '2026-08-08 22:07:14.777928'::timestamp without time zone NOT NULL
);


--
-- Name: master_ukuran_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.master_ukuran_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: master_ukuran_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.master_ukuran_id_seq OWNED BY public.master_ukuran.id;


--
-- Name: master_warna; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.master_warna (
    id integer NOT NULL,
    nama_warna character varying(30) NOT NULL,
    urutan integer DEFAULT 0 NOT NULL,
    status smallint DEFAULT '1'::smallint NOT NULL,
    created_on timestamp without time zone DEFAULT '2026-08-08 22:32:37.067146'::timestamp without time zone NOT NULL
);


--
-- Name: master_warna_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.master_warna_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: master_warna_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.master_warna_id_seq OWNED BY public.master_warna.id;


--
-- Name: opt_agama; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.opt_agama (
    id integer NOT NULL,
    nomor character varying(255),
    nama character varying(255)
);


--
-- Name: opt_kebangsaan; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.opt_kebangsaan (
    id integer NOT NULL,
    nomor smallint,
    nama character varying(255)
);


--
-- Name: opt_kekerasan; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.opt_kekerasan (
    id integer NOT NULL,
    nomor smallint,
    nama character varying(255)
);


--
-- Name: opt_media; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.opt_media (
    id integer NOT NULL,
    nomor character varying(255),
    nama character varying(255)
);


--
-- Name: opt_pekerjaan; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.opt_pekerjaan (
    id integer NOT NULL,
    nomor character varying(255),
    nama character varying(255)
);


--
-- Name: opt_pendidikan; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.opt_pendidikan (
    id integer NOT NULL,
    nomor character varying(255),
    nama character varying(255)
);


--
-- Name: opt_perkawinan; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.opt_perkawinan (
    id integer NOT NULL,
    nomor character varying(255),
    nama character varying(255)
);


--
-- Name: opt_tkp; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.opt_tkp (
    id integer NOT NULL,
    nomor character varying(255),
    nama character varying(255)
);


--
-- Name: order_baju; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.order_baju (
    id integer NOT NULL,
    kode_order character varying(50) NOT NULL,
    nama_customer character varying(100) NOT NULL,
    produk character varying(100) NOT NULL,
    jumlah integer NOT NULL,
    harga numeric(12,2) NOT NULL,
    total_harga numeric(12,2) NOT NULL,
    status_order character varying(30) NOT NULL,
    tanggal_order date NOT NULL,
    ukuran_id integer,
    warna_id integer,
    jenis_baju_id integer,
    created_on timestamp without time zone DEFAULT now() NOT NULL
);


--
-- Name: order_baju_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.order_baju_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: order_baju_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.order_baju_id_seq OWNED BY public.order_baju.id;


--
-- Name: pengaduan_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pengaduan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pengaduan; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pengaduan (
    id integer DEFAULT nextval('public.pengaduan_id_seq'::regclass) NOT NULL,
    nomor_transaksi character varying(255),
    kode_layanan character varying(3),
    tgl_buat timestamp(0) without time zone DEFAULT now(),
    status_approval smallint DEFAULT 0,
    jenis_kasus character varying(255),
    jenis_kasus_rujukan character varying(255),
    bentuk_kekerasan character varying(255),
    bentuk_kekerasan_lain character varying(255),
    hari character varying(255),
    tanggal date,
    konselor character varying(255),
    media character varying(255),
    kronologis text
);


--
-- Name: pengaduan_korban_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pengaduan_korban_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pengaduan_korban; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pengaduan_korban (
    id integer DEFAULT nextval('public.pengaduan_korban_id_seq'::regclass) NOT NULL,
    id_pengaduan integer,
    nama_korban character varying(255),
    jenis_kelamin_korban character varying(255),
    umur_tahun_korban smallint,
    umur_bulan_korban smallint,
    alamat_korban character varying(255),
    provinsi_korban character varying(255),
    kabupaten_korban character varying(255),
    kecamatan_korban character varying(255),
    kelurahan_korban character varying(255),
    rt_korban character varying(3),
    rw_korban character varying(3),
    telp_korban character varying(255),
    pendidikan_korban character varying(255),
    pendidikan_lain_korban character varying(255),
    pekerjaan_korban character varying(255),
    pekerjaan_lain_korban character varying(255),
    agama_korban character varying(255),
    kebangsaan_korban character varying(255),
    kebangsaan_asing_korban character varying(255),
    status_kawin_korban character varying(255),
    status_tkp_korban character varying(255),
    nama_ayah_korban character varying(255),
    nama_ibu_korban character varying(255)
);


--
-- Name: pengaduan_pelaku_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pengaduan_pelaku_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pengaduan_pelaku; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pengaduan_pelaku (
    id integer DEFAULT nextval('public.pengaduan_pelaku_id_seq'::regclass) NOT NULL,
    id_pengaduan integer,
    nama_pelaku character varying(255),
    jenis_kelamin_pelaku character varying(255),
    umur_tahun_pelaku smallint,
    umur_bulan_pelaku smallint,
    alamat_pelaku character varying(255),
    provinsi_pelaku character varying(255),
    kabupaten_pelaku character varying(255),
    kecamatan_pelaku character varying(255),
    kelurahan_pelaku character varying(255),
    rt_pelaku character varying(3),
    rw_pelaku character varying(3),
    telp_pelaku character varying(255),
    pendidikan_pelaku character varying(255),
    pendidikan_lain_pelaku character varying(255),
    pekerjaan_pelaku character varying(255),
    pekerjaan_lain_pelaku character varying(255),
    agama_pelaku character varying(255),
    kebangsaan_pelaku character varying(255),
    kebangsaan_asing_pelaku character varying(255),
    status_kawin_pelaku character varying(255),
    hubungan_pelaku character varying(255)
);


--
-- Name: pengaduan_pelapor_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pengaduan_pelapor_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pengaduan_pelapor; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pengaduan_pelapor (
    id integer DEFAULT nextval('public.pengaduan_pelapor_id_seq'::regclass) NOT NULL,
    id_pengaduan integer,
    nama_pelapor character varying(255),
    jenis_kelamin_pelapor character varying(255),
    umur_tahun_pelapor smallint,
    umur_bulan_pelapor smallint,
    alamat_pelapor character varying(255),
    provinsi_pelapor character varying(255),
    kabupaten_pelapor character varying(255),
    kecamatan_pelapor character varying(255),
    kelurahan_pelapor character varying(255),
    rt_pelapor character varying(3),
    rw_pelapor character varying(3),
    telp_pelapor character varying(255),
    pendidikan_pelapor character varying(255),
    pendidikan_lain_pelapor character varying(255),
    pekerjaan_pelapor character varying(255),
    pekerjaan_lain_pelapor character varying(255),
    hubungan_pelapor character varying(255)
);


--
-- Name: pengaduanku; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pengaduanku (
    id integer DEFAULT nextval('public.pengaduan_id_seq'::regclass) NOT NULL,
    nomor_transaksi character varying(255),
    kode_layanan character varying(3),
    tgl_buat timestamp(6) without time zone DEFAULT now(),
    id_pemohon integer DEFAULT 0,
    deleted smallint DEFAULT 0,
    jenis_kasus character varying(255),
    jenis_kasus_rujukan character varying(255),
    bentuk_kekerasan character varying(255),
    bentuk_kekerasan_lain character varying(255),
    hari character varying(255),
    tanggal date,
    konselor character varying(255),
    media character varying(255),
    kronologis text
);


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permissions (
    permission_id integer NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(100) NOT NULL,
    status character varying(8) DEFAULT 'active'::character varying NOT NULL
);


--
-- Name: permissions_permission_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.permissions_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: permissions_permission_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.permissions_permission_id_seq OWNED BY public.permissions.permission_id;


--
-- Name: reg_districts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reg_districts (
    id character(6) NOT NULL,
    regency_id character(4) NOT NULL,
    name character varying(255) NOT NULL
);


--
-- Name: reg_provinces; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reg_provinces (
    id character(2) NOT NULL,
    name character varying(255) NOT NULL
);


--
-- Name: reg_regencies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reg_regencies (
    id character(4) NOT NULL,
    province_id character(2) NOT NULL,
    name character varying(255) NOT NULL
);


--
-- Name: reg_villages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reg_villages (
    id character(10) NOT NULL,
    district_id character(6) NOT NULL,
    name character varying(255) NOT NULL
);


--
-- Name: registrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.registrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: registrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.registrations (
    id integer DEFAULT nextval('public.registrations_id_seq'::regclass) NOT NULL,
    email character varying(255) NOT NULL,
    nik character varying(255) NOT NULL,
    nama character varying(255) NOT NULL,
    jenis_kelamin character varying(255) NOT NULL,
    tgl_lahir date NOT NULL,
    alamat character varying(500) NOT NULL,
    user_id integer,
    role_id integer,
    file_code character varying,
    no_telp character varying(255),
    kecamatan character varying(255),
    kelurahan character varying(255)
);


--
-- Name: report; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.report (
    id integer NOT NULL,
    periode character varying(20) NOT NULL,
    tgl_mulai date,
    tgl_akhir date,
    jumlah_transaksi integer DEFAULT 0 NOT NULL,
    total_nilai numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    created_on timestamp without time zone DEFAULT (now() AT TIME ZONE 'UTC'::text) NOT NULL,
    tipe_report character varying(10) DEFAULT 'pdf'::character varying NOT NULL,
    nama_file character varying(255),
    path_file character varying(255)
);


--
-- Name: report_backup_20260815; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.report_backup_20260815 (
    id integer,
    periode character varying(20),
    tgl_mulai date,
    tgl_akhir date,
    jumlah_transaksi integer,
    total_nilai numeric(12,2),
    nama_file_pdf character varying(255),
    path_file_pdf character varying(255),
    created_on timestamp without time zone,
    nama_file_excel character varying(255),
    path_file_excel character varying(255)
);


--
-- Name: report_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.report_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: report_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.report_id_seq OWNED BY public.report.id;


--
-- Name: role_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role_permissions (
    role_id integer NOT NULL,
    permission_id integer NOT NULL
);


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    role_id integer NOT NULL,
    role_name character varying(60) NOT NULL,
    description character varying(255) NOT NULL,
    "default" smallint DEFAULT 0 NOT NULL,
    can_delete smallint DEFAULT 0 NOT NULL,
    login_destination character varying(255) DEFAULT '/'::character varying NOT NULL,
    default_context character varying(255) DEFAULT 'content'::character varying NOT NULL,
    deleted smallint DEFAULT 0 NOT NULL
);


--
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_role_id_seq OWNED BY public.roles.role_id;


--
-- Name: schema_version; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.schema_version (
    type character varying(40) NOT NULL,
    version integer NOT NULL
);


--
-- Name: services_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: services; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.services (
    id integer DEFAULT nextval('public.services_id_seq'::regclass) NOT NULL,
    kode character varying(255),
    nama character varying(255),
    "table" character varying(255)
);


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    session_id character varying(40) NOT NULL,
    ip_address character varying(45) NOT NULL,
    user_agent character varying(120) NOT NULL,
    last_activity bigint NOT NULL,
    user_data text NOT NULL
);


--
-- Name: settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.settings (
    name character varying(30) NOT NULL,
    module character varying(255) NOT NULL,
    value character varying(255) NOT NULL
);


--
-- Name: sk_tidak_mampu; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sk_tidak_mampu (
    id integer NOT NULL,
    nama character varying(255),
    alamat character varying(255),
    jenis_surat character varying(255),
    no_telepon character varying(255),
    tanggal date
);


--
-- Name: sk_tidak_mampu_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sk_tidak_mampu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sk_tidak_mampu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sk_tidak_mampu_id_seq OWNED BY public.sk_tidak_mampu.id;


--
-- Name: transaksi; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.transaksi (
    id integer NOT NULL,
    order_baju_id integer NOT NULL,
    jumlah integer NOT NULL,
    harga numeric(12,2) NOT NULL,
    total_harga numeric(12,2) NOT NULL,
    status_transaksi character varying(30) DEFAULT 'Draft'::character varying NOT NULL,
    created_on timestamp without time zone DEFAULT '2026-08-10 14:50:56.83571'::timestamp without time zone NOT NULL,
    dokumen text
);


--
-- Name: transaksi_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.transaksi_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: transaksi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.transaksi_id_seq OWNED BY public.transaksi.id;


--
-- Name: user_cookies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_cookies (
    user_id bigint NOT NULL,
    token character varying(128) NOT NULL,
    created_on timestamp(6) without time zone NOT NULL
);


--
-- Name: user_cookies_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_cookies_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_cookies_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_cookies_user_id_seq OWNED BY public.user_cookies.user_id;


--
-- Name: user_meta; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_meta (
    meta_id bigint NOT NULL,
    user_id bigint NOT NULL,
    meta_key character varying(255) NOT NULL,
    meta_value text
);


--
-- Name: user_meta_meta_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_meta_meta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_meta_meta_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_meta_meta_id_seq OWNED BY public.user_meta.meta_id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    role_id integer NOT NULL,
    email character varying(254) NOT NULL,
    username character varying(30) NOT NULL,
    password_hash character(60) NOT NULL,
    reset_hash character varying(40),
    last_login timestamp(6) without time zone,
    last_ip character varying(45) NOT NULL,
    created_on timestamp(6) without time zone NOT NULL,
    deleted smallint DEFAULT 0 NOT NULL,
    reset_by integer,
    banned smallint NOT NULL,
    ban_message character varying(255),
    display_name character varying(255) NOT NULL,
    display_name_changed date,
    timezone character varying(40) NOT NULL,
    language character varying(20) NOT NULL,
    active smallint NOT NULL,
    activate_hash character varying(40) NOT NULL,
    force_password_reset smallint
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: activities activity_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.activities ALTER COLUMN activity_id SET DEFAULT nextval('public.activities_activity_id_seq'::regclass);


--
-- Name: backup_database_history id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_database_history ALTER COLUMN id SET DEFAULT nextval('public.backup_database_history_id_seq'::regclass);


--
-- Name: backup_document_history id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_document_history ALTER COLUMN id SET DEFAULT nextval('public.backup_document_history_id_seq'::regclass);


--
-- Name: email_queue id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.email_queue ALTER COLUMN id SET DEFAULT nextval('public.email_queue_id_seq'::regclass);


--
-- Name: login_attempts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.login_attempts ALTER COLUMN id SET DEFAULT nextval('public.login_attempts_id_seq'::regclass);


--
-- Name: master_jenis_baju id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.master_jenis_baju ALTER COLUMN id SET DEFAULT nextval('public.master_jenis_baju_id_seq'::regclass);


--
-- Name: master_ukuran id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.master_ukuran ALTER COLUMN id SET DEFAULT nextval('public.master_ukuran_id_seq'::regclass);


--
-- Name: master_warna id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.master_warna ALTER COLUMN id SET DEFAULT nextval('public.master_warna_id_seq'::regclass);


--
-- Name: order_baju id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_baju ALTER COLUMN id SET DEFAULT nextval('public.order_baju_id_seq'::regclass);


--
-- Name: permissions permission_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions ALTER COLUMN permission_id SET DEFAULT nextval('public.permissions_permission_id_seq'::regclass);


--
-- Name: report id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.report ALTER COLUMN id SET DEFAULT nextval('public.report_id_seq'::regclass);


--
-- Name: roles role_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN role_id SET DEFAULT nextval('public.roles_role_id_seq'::regclass);


--
-- Name: sk_tidak_mampu id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sk_tidak_mampu ALTER COLUMN id SET DEFAULT nextval('public.sk_tidak_mampu_id_seq'::regclass);


--
-- Name: transaksi id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transaksi ALTER COLUMN id SET DEFAULT nextval('public.transaksi_id_seq'::regclass);


--
-- Name: user_cookies user_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_cookies ALTER COLUMN user_id SET DEFAULT nextval('public.user_cookies_user_id_seq'::regclass);


--
-- Name: user_meta meta_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_meta ALTER COLUMN meta_id SET DEFAULT nextval('public.user_meta_meta_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: activities activities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.activities
    ADD CONSTRAINT activities_pkey PRIMARY KEY (activity_id);


--
-- Name: approval_items_desc approval_items_desc_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.approval_items_desc
    ADD CONSTRAINT approval_items_desc_pkey PRIMARY KEY (id);


--
-- Name: approval approval_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.approval
    ADD CONSTRAINT approval_pkey PRIMARY KEY (id);


--
-- Name: authority_items authority_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.authority_items
    ADD CONSTRAINT authority_items_pkey PRIMARY KEY (id);


--
-- Name: authority authority_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.authority
    ADD CONSTRAINT authority_pkey PRIMARY KEY (id);


--
-- Name: backup_database_history backup_database_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_database_history
    ADD CONSTRAINT backup_database_history_pkey PRIMARY KEY (id);


--
-- Name: backup_document_history backup_document_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_document_history
    ADD CONSTRAINT backup_document_history_pkey PRIMARY KEY (id);


--
-- Name: ci3_sessions ci3_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ci3_sessions
    ADD CONSTRAINT ci3_sessions_pkey PRIMARY KEY (id);


--
-- Name: email_queue email_queue_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.email_queue
    ADD CONSTRAINT email_queue_pkey PRIMARY KEY (id);


--
-- Name: opt_perkawinan kt_opt_agama_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.opt_perkawinan
    ADD CONSTRAINT kt_opt_agama_copy1_pkey PRIMARY KEY (id);


--
-- Name: opt_media kt_opt_kekerasan_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.opt_media
    ADD CONSTRAINT kt_opt_kekerasan_copy1_pkey PRIMARY KEY (id);


--
-- Name: opt_kekerasan kt_opt_kekerasan_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.opt_kekerasan
    ADD CONSTRAINT kt_opt_kekerasan_pkey PRIMARY KEY (id);


--
-- Name: opt_pendidikan kt_opt_media_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.opt_pendidikan
    ADD CONSTRAINT kt_opt_media_copy1_pkey PRIMARY KEY (id);


--
-- Name: opt_pekerjaan kt_opt_pendidikan_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.opt_pekerjaan
    ADD CONSTRAINT kt_opt_pendidikan_copy1_pkey PRIMARY KEY (id);


--
-- Name: opt_agama kt_opt_pendidikan_copy1_pkey1; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.opt_agama
    ADD CONSTRAINT kt_opt_pendidikan_copy1_pkey1 PRIMARY KEY (id);


--
-- Name: opt_kebangsaan kt_opt_pendidikan_copy1_pkey2; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.opt_kebangsaan
    ADD CONSTRAINT kt_opt_pendidikan_copy1_pkey2 PRIMARY KEY (id);


--
-- Name: opt_tkp kt_opt_perkawinan_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.opt_tkp
    ADD CONSTRAINT kt_opt_perkawinan_copy1_pkey PRIMARY KEY (id);


--
-- Name: pengaduan_korban kt_pengaduan_pelapor_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pengaduan_korban
    ADD CONSTRAINT kt_pengaduan_pelapor_copy1_pkey PRIMARY KEY (id);


--
-- Name: pengaduan_pelaku kt_pengaduan_pelapor_copy1_pkey1; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pengaduan_pelaku
    ADD CONSTRAINT kt_pengaduan_pelapor_copy1_pkey1 PRIMARY KEY (id);


--
-- Name: pengaduan_pelapor kt_pengaduan_pelapor_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pengaduan_pelapor
    ADD CONSTRAINT kt_pengaduan_pelapor_pkey PRIMARY KEY (id);


--
-- Name: pengaduan kt_pengaduan_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pengaduan
    ADD CONSTRAINT kt_pengaduan_pkey PRIMARY KEY (id);


--
-- Name: login_attempts login_attempts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.login_attempts
    ADD CONSTRAINT login_attempts_pkey PRIMARY KEY (id);


--
-- Name: pengaduanku pengaduanku_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pengaduanku
    ADD CONSTRAINT pengaduanku_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (permission_id);


--
-- Name: master_jenis_baju pk_master_jenis_baju; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.master_jenis_baju
    ADD CONSTRAINT pk_master_jenis_baju PRIMARY KEY (id);


--
-- Name: master_ukuran pk_master_ukuran; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.master_ukuran
    ADD CONSTRAINT pk_master_ukuran PRIMARY KEY (id);


--
-- Name: master_warna pk_master_warna; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.master_warna
    ADD CONSTRAINT pk_master_warna PRIMARY KEY (id);


--
-- Name: order_baju pk_order_baju; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_baju
    ADD CONSTRAINT pk_order_baju PRIMARY KEY (id);


--
-- Name: report pk_report; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.report
    ADD CONSTRAINT pk_report PRIMARY KEY (id);


--
-- Name: transaksi pk_transaksi; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT pk_transaksi PRIMARY KEY (id);


--
-- Name: reg_districts reg_districts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reg_districts
    ADD CONSTRAINT reg_districts_pkey PRIMARY KEY (id);


--
-- Name: reg_provinces reg_provinces_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reg_provinces
    ADD CONSTRAINT reg_provinces_pkey PRIMARY KEY (id);


--
-- Name: reg_regencies reg_regencies_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reg_regencies
    ADD CONSTRAINT reg_regencies_pkey PRIMARY KEY (id);


--
-- Name: reg_villages reg_villages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reg_villages
    ADD CONSTRAINT reg_villages_pkey PRIMARY KEY (id);


--
-- Name: registrations registration_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registrations
    ADD CONSTRAINT registration_pkey PRIMARY KEY (id);


--
-- Name: role_permissions role_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_pkey PRIMARY KEY (role_id, permission_id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (role_id);


--
-- Name: schema_version schema_version_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.schema_version
    ADD CONSTRAINT schema_version_pkey PRIMARY KEY (type);


--
-- Name: services services_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (session_id);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (name);


--
-- Name: sk_tidak_mampu sk_tidak_mampu_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sk_tidak_mampu
    ADD CONSTRAINT sk_tidak_mampu_pkey PRIMARY KEY (id);


--
-- Name: user_meta user_meta_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_meta
    ADD CONSTRAINT user_meta_pkey PRIMARY KEY (meta_id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: districts_regency_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX districts_regency_id_index ON public.reg_districts USING btree (regency_id);


--
-- Name: email; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX email ON public.users USING btree (email);


--
-- Name: idx_report_created_on; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_report_created_on ON public.report USING btree (created_on);


--
-- Name: idx_report_tipe; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_report_tipe ON public.report USING btree (tipe_report);


--
-- Name: regencies_province_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX regencies_province_id_index ON public.reg_regencies USING btree (province_id);


--
-- Name: token; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX token ON public.user_cookies USING btree (token);


--
-- Name: uq_transaksi_order_baju; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uq_transaksi_order_baju ON public.transaksi USING btree (order_baju_id);


--
-- Name: villages_district_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX villages_district_id_index ON public.reg_villages USING btree (district_id);


--
-- Name: reg_districts district_regency_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reg_districts
    ADD CONSTRAINT district_regency_foreign FOREIGN KEY (regency_id) REFERENCES public.reg_regencies(id);


--
-- Name: order_baju fk_order_baju_jenis; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_baju
    ADD CONSTRAINT fk_order_baju_jenis FOREIGN KEY (jenis_baju_id) REFERENCES public.master_jenis_baju(id);


--
-- Name: order_baju fk_order_baju_ukuran; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_baju
    ADD CONSTRAINT fk_order_baju_ukuran FOREIGN KEY (ukuran_id) REFERENCES public.master_ukuran(id);


--
-- Name: order_baju fk_order_baju_warna; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.order_baju
    ADD CONSTRAINT fk_order_baju_warna FOREIGN KEY (warna_id) REFERENCES public.master_warna(id);


--
-- Name: transaksi fk_transaksi_order_baju; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT fk_transaksi_order_baju FOREIGN KEY (order_baju_id) REFERENCES public.order_baju(id);


--
-- Name: reg_regencies regency_province_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reg_regencies
    ADD CONSTRAINT regency_province_foreign FOREIGN KEY (province_id) REFERENCES public.reg_provinces(id);


--
-- Name: reg_villages village_district_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reg_villages
    ADD CONSTRAINT village_district_foreign FOREIGN KEY (district_id) REFERENCES public.reg_districts(id);


--
-- PostgreSQL database dump complete
--

