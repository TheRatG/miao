import sys, os, shutil
import config

class InvalidClassName(Exception):
	pass
class FileExists(Exception):
	pass
class InvalidCommand(Exception):
	pass
class FileNotFound(Exception):
	pass
	
def get_root():
	return os.path.dirname(sys.argv[0])

def get_templates_root():
	return get_root() + '/templates'

class ClassName:
	full_name = ''
	def __init__(self, name, validate = False):
		items = name.split('_')
		self.full_name = name
		self.project = items[0] if len(items) >= 1 else ''
		self.module = items[1] if len(items) >= 2 else ''
		self.resource = items[2] if len(items) >= 3 else ''
		self.name = '_'.join(items[3:]) if len(items) >= 4 else ''
		if validate:
			self.validate()
	def validate(self):
		if not self.project:
			raise InvalidClassName('Empty project in "%s"' % self.full_name)
		try:
			config.get('libs/' + self.project + '/deploy/dst')
		except config.PropertyNotFound:
			raise InvalidClassName('Non-existent project in "%s"' % self.full_name)
		if not self.module:
			raise InvalidClassName('Empty module in "%s"' % self.full_name)
		if self.module not in ['FrontOffice', 'BackOffice']:
			raise InvalidClassName('Invalid module in "%s" (must be FrontOffice|BackOffice)' % self.full_name)
		if not self.resource:
			raise InvalidClassName('Empty resource in "%s"' % self.full_name)
		if self.resource not in ['View', 'ViewBlock', 'Action']:
			raise InvalidClassName('Invalid resource in "%s" (must be View|ViewBlock|Action)' % self.full_name)
		if not self.name:
			raise InvalidClassName('Empty class name in "%s"' % self.full_name)
			
class ClassPath:
	def __init__(self, name):
		self.class_name = ClassName(name, validate = True)
		self.root = config.get('libs/' + self.class_name.project + '/deploy/dst')
		self.templates_root = get_templates_root()
	def get_class_src(self):
		return '%s/%s.class.php' % (self.templates_root, self.class_name.resource)
	def get_class_dst(self):
		return '%s/modules/%s/classes/%s/%s.class.php' % (self.root, self.class_name.module, self.class_name.resource, '/'.join(self.class_name.name.split('_')))
	def get_tpl_src(self):
		if self.class_name.resource in ('View', 'ViewBlock'):
			path = '%s/%s.tpl' % (self.templates_root, self.class_name.resource.lower())
		else:
			path = ''
		return path
	def get_tpl_dst(self):
		if self.class_name.resource == 'View':
			path = '%s/modules/%s/templates/%s/%s.tpl' % (self.root, self.class_name.module, self.class_name.resource, self.class_name.name.lower())
		elif self.class_name.resource  == 'ViewBlock':
			path = '%s/modules/%s/templates/%s/%s/index.tpl' % (self.root, self.class_name.module, self.class_name.resource, '/'.join(self.class_name.name.split('_')))
		elif self.class_name.resource  == 'Action':
			path = ''
		return path 

#class_path1 = ClassPath('Teleprog_FrontOffice_View_Main')
#print class_path1.get_class_src()
#print class_path1.get_class_dst()
#print class_path1.get_tpl_src()
#print class_path1.get_tpl_dst()
#print
#class_path2 = ClassPath('Teleprog_FrontOffice_ViewBlock_Main')
#print class_path2.get_class_src()
#print class_path2.get_class_dst()
#print class_path2.get_tpl_src()
#print class_path2.get_tpl_dst()
#print
#class_path3 = ClassPath('Teleprog_FrontOffice_Action_Main')
#print class_path3.get_class_src()
#print class_path3.get_class_dst()
#print class_path3.get_tpl_src()
#print class_path3.get_tpl_dst()
#print

def copy_file(src, dst, replace = {}):
	if not src or not dst:
		return
	if not os.path.isfile(src):
		raise FileNotFound('File "%s" not found' % src)
	elif os.path.isfile(dst):
		raise FileExists('File "%s" already exists' % dst)
	if not os.path.isdir(os.path.dirname(dst)):
		os.makedirs(os.path.dirname(dst))
	print
	print 'Copying file...'
	print 'SRC:', src
	print 'DST:', dst
	shutil.copyfile(src, dst)
	if len(replace):
		text = file_get_contents(dst)
		for k,v in replace.items():
			print 'MAP:', k, '=>', v
			text = text.replace(k, v)
		file_put_contents(dst, text)
	
def file_get_contents(file):
	f = open(file, 'r')
	content = f.read()
	f.close()
	return content
	
def file_put_contents(file, content):
	f = open(file, 'w')
	f.write(content)
	f.close()
	
def remove_file(path):
	if not path:
		return
	if not os.path.isfile(path):
		raise FileNotFound('Unable to delete "%s"' % path)
	print
	print 'DEL:', path
	os.remove(path)
	
def add(name):
	class_path = ClassPath(name)
	replace = {'%LOGIN%':os.getlogin(),'%CLASS%':name}
	copy_file(class_path.get_class_src(), class_path.get_class_dst(), replace)
	copy_file(class_path.get_tpl_src(), class_path.get_tpl_dst(), replace)
	
def ren(name1, name2):
	class_path1, class_path2 = ClassPath(name1), ClassPath(name2)
	copy_file(class_path1.get_class_dst(), class_path2.get_class_dst())
	remove_file(class_path1.get_class_dst())
	copy_file(class_path1.get_tpl_dst(), class_path2.get_tpl_dst())
	remove_file(class_path1.get_tpl_dst())
	
def remove(name):
	class_path = ClassPath(name)
	remove_file(class_path.get_class_dst())
	remove_file(class_path.get_tpl_dst())
	
def process(list):
	if list[0].lower() == 'add':
		if len(list) == 2:
			add(list[1])
		else:
			raise InvalidCommand('Wrong number of arguments')
	elif list[0].lower() == 'ren':
		if len(list) == 3:
			ren(list[1], list[2])
		else:
			raise InvalidCommand('Wrong number of arguments')
	elif list[0].lower() == 'del':
		if len(list) == 2:
			remove(list[1])
		else:
			raise InvalidCommand('Wrong number of arguments')
