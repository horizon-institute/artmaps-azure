//
//  OoIMetaLoader.m
//  WordPress
//
//  Created by Shakir Ali on 21/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//
#import "JSONKit.h"

#import "OoIMetaLoader.h"
#import "RestAPIConnector.h"
#import "DataParser.h"

@implementation OoIMetaLoader
@synthesize delegate;
@synthesize refObjID;
@synthesize indexPathInTableView;

-(void)submitOoIMetaRequestWithID:(NSNumber*)metaID{
    [self initConnectionRequest];
    NSURLRequest* urlRequest = [self prepareOoIMetaRequestWithID:metaID];
    [self submitURLRequest:urlRequest];
}

-(void)submitOoIMetaRequestWithID:(NSNumber*)metaID forIndexPathInTableView:(NSIndexPath*)indexPath{
    self.indexPathInTableView = indexPath;
    [self submitOoIMetaRequestWithID:metaID];
}

-(NSURLRequest*)prepareOoIMetaRequestWithID:(NSNumber*)metaID{
    NSString* metaUrl = [[RestAPIConnector sharedInstance] getOoIMetaURLWithMetaID:metaID];
    NSURL* url = [NSURL URLWithString:metaUrl];
    return [NSURLRequest requestWithURL:url];
}

#pragma mark NSURLConnectionDelegate functions.
- (void)connectionDidFinishLoading:(NSURLConnection *)connection{
    [super connectionDidFinishLoading:connection];
    NSDictionary *data= [self.jsonData objectFromJSONData];
    OoIMeta* ooiMeta = [DataParser getOoIMetaFromDictionary:data];
    if (self.indexPathInTableView == nil){
        if ([self.delegate respondsToSelector:@selector(OoIMetaLoader:didLoadOoIMeta:)])
            [delegate OoIMetaLoader:self didLoadOoIMeta:ooiMeta];
    }
    else{
        if ([self.delegate respondsToSelector:@selector(metaDataDidLoad:forIndexPath:)])
            [delegate metaDataDidLoad:ooiMeta forIndexPath:indexPathInTableView];
    }    	
}

- (void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error{
    [super connection:connection didFailWithError:error];
}

-(void)cancelMetaLoad{
    [self cancelRequest];
    self.delegate = nil;
}

-(void)dealloc{
    delegate = nil;
    [refObjID release];
    [indexPathInTableView release];
    [super dealloc];
}

@end
