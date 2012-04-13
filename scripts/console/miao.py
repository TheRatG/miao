import sys, os, getopt

sys.path.append(os.path.dirname(sys.argv[0]) + '/modules')
import opts

opts.printHeader()
config_file = ''

def main():    
    lArgs = opts.parseOptions()
    try:
        import processor
        processor.process(lArgs)
    except Exception, msg:
        print 'ERROR (%s): %s' % (msg.__class__.__name__, msg)        
        sys.exit(2)
        
if __name__ == "__main__":
    main()